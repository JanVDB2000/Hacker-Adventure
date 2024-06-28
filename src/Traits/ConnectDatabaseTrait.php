<?php

namespace Traits;

use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

trait ConnectDatabaseTrait
{

    /**
     * Resolve data from the database.
     *
     * @throws SQL
     * @throws \Exception
     */
    protected function resolveFromDatabase()
    {
        $this->id ??= 0;
        $dbRecord = R::findOne($this->tablename, 'id = ?', [$this->id]);

        if ($dbRecord) {
            $this->id = $dbRecord->id;
            foreach ($this->getDatabaseFields() as $field) {
                $this->$field = $dbRecord->$field;
            }
            $this->save();
            return $dbRecord->id;
        } else {
            $this->save();
        }
    }

    /**
     * Save or update the record in the database.
     *
     * @throws SQL
     * @throws \Exception
     */
    public function save()
    {
        $dbRecord = R::load($this->tablename, $this->id);

        if (!$dbRecord) {
            $dbRecord = R::dispense($this->tablename);
        }

        foreach ($this->getDatabaseFields() as $field) {
            $dbRecord->$field = $this->$field;
        }

        R::store($dbRecord);

        return $dbRecord->getID();
    }

    public function getDatabaseFields(): array
    {
        if (R::inspect($this->tablename)) {
            return array_keys(R::inspect($this->tablename));
        } else {
           return $this->makeDatabaseTabel();
        }
    }

    private function makeDatabaseTabel()
    {
        $fields = $this->getFieldsFromModel();
        $bean = R::dispense($this->tablename);
        foreach ($fields as $fieldName) {
            $bean->$fieldName = $this->$fieldName;
        }

        R::store($bean);

        return array_keys($fields);
    }

    private function getFieldsFromModel(): array
    {
        $ref = new \ReflectionClass($this);

        $arrayTables = [];
        foreach ($ref->getProperties() as $property){
            if ($property->getName() !== 'id' ){
                if ($property->getName() !== 'tablename'){
                    $arrayTables[] = $property->getName();
                }
            }
        }
        return $arrayTables;
    }


}
