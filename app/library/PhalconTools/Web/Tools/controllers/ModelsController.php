<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

use Phalcon\Tag;
use Phalcon\Web\Tools;
use Phalcon\Builder\BuilderException;

class ModelsController extends ControllerBase
{

	public function indexAction()
	{

		$config = Tools::getConfig();
		$connection = Tools::getConnection();

        if (Tools::getConfig()->modules) {
            $tables = array();
        }
        else{
            $tables = array('all' => 'All');
        }

		$result = $connection->query("SHOW TABLES");
		$result->setFetchMode(Phalcon\DB::FETCH_NUM);
		while($table = $result->fetchArray($result)){
			$tables[$table[0]] = $table[0];
		}

        if (Tools::getConfig()->modules) {
            $this->view->setVar('hasModules', true);
            $modules = array();
            foreach (Tools::getConfig()->modules as $moduleName => $enabled) {
                if (!$enabled) continue;
                $modules[$moduleName] = $moduleName;
            }
            $this->view->setVar('modules', $modules);
        } else {
            $this->view->setVar('hasModules', false);
        }

		$this->view->setVar('tables', $tables);
		$this->view->setVar('databaseName', $config->database->name);
	}

	/**
	 * Generate models
	 */
	public function createAction()
	{

		if ($this->request->isPost()) {

			$force = $this->request->getPost('force', 'int');
			$schema = $this->request->getPost('schema');
			$tableName = $this->request->getPost('tableName');
            $moduleName = $this->request->getPost('moduleName');
			$genSettersGetters = $this->request->getPost('genSettersGetters', 'int');
			$foreignKeys = $this->request->getPost('foreignKeys', 'int');
			$defineRelations = $this->request->getPost('defineRelations', 'int');

			try {

                if ($moduleName){
                    $component = '\Phalcon\Builder\Model';
                    $modelBuilder = new $component(array(
                        'name' 					=> $tableName,
                        'force' 				=> $force,
                        'modelsDir' 			=> ROOT_PATH . "/app/modules/{$moduleName}/models/",
                        'namespace'             => $moduleName."\Models",
                        'directory' 			=> null,
                        'foreignKeys' 			=> $foreignKeys,
                        'defineRelations' 		=> $defineRelations,
                        'genSettersGetters' 	=> $genSettersGetters

                    ));

                    $modelBuilder->build();

                    if ($tableName == 'all') {
                        $this->flash->success('Models were created successfully');
                    } else {
                        $this->flash->success('Model "'.$tableName.'" was created successfully');
                    }
                }
                else{
                    $component = '\Phalcon\Builder\Model';
                    if ($tableName == 'all') {
                        $component = '\Phalcon\Builder\AllModels';
                    }

                    $modelBuilder = new $component(array(
                        'name' 					=> $tableName,
                        'force' 				=> $force,
                        'modelsDir' 			=> Tools::getConfig()->application->modelsDir,
                        'directory' 			=> null,
                        'foreignKeys' 			=> $foreignKeys,
                        'defineRelations' 		=> $defineRelations,
                        'genSettersGetters' 	=> $genSettersGetters

                    ));

                    $modelBuilder->build();

                    if ($tableName == 'all') {
                        $this->flash->success('Models were created successfully');
                    } else {
                        $this->flash->success('Model "'.$tableName.'" was created successfully');
                    }
                }

			}
			catch(BuilderException $e){
				$this->flash->error($e->getMessage());
			}

		}

		return $this->dispatcher->forward(array(
			'controller' => 'models',
			'action' => 'list'
		));

	}

	public function listAction()
	{
        if (Tools::getConfig()->modules) {
            $this->view->setVar('hasModules', true);
            $this->view->setVar('modules', Tools::getConfig()->modules);
        } else {
            $this->view->setVar('hasModules', false);
            $this->view->setVar('modelsDir', Tools::getConfig()->application->modelsDir);
        }
	}

	public function editAction($fileName)
	{

		$fileName = str_replace('..', '', $fileName);

        if (Tools::getConfig()->modules) {
            $modelsDir = ROOT_PATH . "/app/modules/{$this->request->get('_module', 'string')}/models/";
        } else {
            $modelsDir = Tools::getConfig()->application->modelsDir;
        }


		if(!file_exists($modelsDir.'/'.$fileName)){
			$this->flash->error('Model could not be found');
			return $this->dispatcher->forward(array(
				'controller' => 'models',
				'action' => 'list'
			));
		}

		Tag::setDefault('code', file_get_contents($modelsDir.'/'.$fileName));
		Tag::setDefault('name', $fileName);
		$this->view->setVar('name', $fileName);
        $this->view->setVar('moduleName', $this->request->get('_module', 'string'));

	}

	public function saveAction()
	{

		if ($this->request->isPost()) {

			$fileName = $this->request->getPost('name', 'string');

			$fileName = str_replace('..', '', $fileName);

            if (Tools::getConfig()->modules) {
                $modelsDir = ROOT_PATH . "/app/modules/{$this->request->get('_module', 'string')}/models/";
            } else {
                $modelsDir = Tools::getConfig()->application->modelsDir;
            }
			if(!file_exists($modelsDir.'/'.$fileName)){
				$this->flash->error('Model could not be found');
				return $this->dispatcher->forward(array(
					'controller' => 'models',
					'action' => 'list'
				));
			}

			if(!is_writable($modelsDir.'/'.$fileName)){
				$this->flash->error('Model file does not has write access');
				return $this->dispatcher->forward(array(
					'controller' => 'models',
					'action' => 'list'
				));
			}

			file_put_contents($modelsDir.'/'.$fileName, $this->request->getPost('code'));

			$this->flash->success('The model "'.$fileName.'" was saved successfully');

		}

		return $this->dispatcher->forward(array(
			'controller' => 'models',
			'action' => 'list'
		));

	}

}