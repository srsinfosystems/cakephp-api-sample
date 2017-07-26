<?php
class PostsController extends AppController {
	
	public $components = array('RequestHandler');

	public $uses = array('User');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->deny('*'); //Disallow access to all actions
        $this->Auth->allow('index'); //Allow access to index()
        $this->Auth->allow('initDB');

        $this->Acl->Aro = ClassRegistry::init('AclAro');
		$this->Acl->Aco = ClassRegistry::init('AclAco');
		$this->Acl->AcoAros = ClassRegistry::init('AclPermission');
    }
	
    public function index() {
        
        //throw new UnauthorizedToAccessException();
		//throw new NotFoundException('Could not find that post');
		//throw new MissingWidgetException('Could not find that post');
        
        //debug($this->request->data);
        
        //$this->set('posts', $this->paginate());

        /*$this->redirect(array(
            'controller'=>'users', 
            'action'=>'login' 
        ));*/


        /*$ath = $this->Auth->user();
        debug($ath); exit;*/

        $response = array();
        
        $response['requestdata'] =  $this->request->data;
        $response['request'] =  $_REQUEST;
        $response['header'] =  $this->request->header('api_key');
        
        $this->set(array(
            'Apitest' => $response,
            '_serialize' => array('Apitest')
        ));
    }

    public function view() {
		$id = $this->getPostData('id');
        $posts = $this->Post->findById($id);
        $this->set(array(
            'posts' => $posts,
            '_serialize' => array('posts')
        ));
    }

    public function edit($id) {
        $this->Post->id = $id;
        if ($this->Post->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function delete($id) {
        if ($this->Post->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }


	public function initDB() {
	    $role = $this->User->Role;

	    // Allow admins to everything
	    $role->id = 1;
	    $this->Acl->allow($role, 'controllers');

	    // allow managers to posts and widgets
	    $role->id = 2;
	    $this->Acl->deny($role, 'controllers');
	    $this->Acl->allow($role, 'controllers/Posts');

	    // allow users to only add and edit on posts and widgets
	    $role->id = 4;
	    $this->Acl->deny($role, 'controllers');
	    $this->Acl->allow($role, 'controllers/Posts/index');
	    $this->Acl->allow($role, 'controllers/Posts/view');

	    // we add an exit to avoid an ugly "missing views" error message
	    echo "all done";
	    exit;
	}
    
}