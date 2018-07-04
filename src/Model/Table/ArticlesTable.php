<?php

// src/Model/Table/ArticlesTable.php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Table;
use Cake\Utility\Text;

class articlesTable extends Table {

    public function initialize(array $config) {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags'); // Add this line
    }

    public function beforeSave($event, $entity, $options) {
        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // trim slug to maximum length defined in schema
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    // in src/Controller/ArticlesController.php
// Add the following method.

    public function edit($slug) {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }
        $this->set('article', $article);
    }

    // Add the following method.
    public function validationDefault(Validator $validator) {
        $validator
                ->notEmpty('title')
                ->minLength('title', 10)
                ->maxLength('title', 255)
                ->notEmpty('body')
                ->minLength('body', 10);

        return $validator;
    }

}
