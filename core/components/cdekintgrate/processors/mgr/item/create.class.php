<?php

class cdekIntgrateItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'cdekIntgrateItem';
    public $classKey = 'cdekIntgrateItem';
    public $languageTopics = ['cdekintgrate:manager'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('cdekintgrate_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('cdekintgrate_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'cdekIntgrateItemCreateProcessor';