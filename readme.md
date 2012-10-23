CakePHP Mixpanel Plugin
=======================

This plugin provides a Mixpanel component to track events from your controllers.
Then you use the provided Mixpanel helper to spit out the generated javascript
into the next generated page. Modeled after the work by felixge:

[https://github.com/felixge/cakephp-mixpanel](https://github.com/felixge/cakephp-mixpanel)

The plugin is written for CakePHP 2.0, but there is 
[a branch for 1.3](https://github.com/noahm/CakePHP-Mixpanel/tree/1.3)
backported by [dkullmann](https://github.com/dkullmann).

How to Use
----------

    # git clone git://github.com/noahm/CakePHP-Mixpanel.git app/Plugin/Mixpanel

Alternatively:

    # git submodule add git://github.com/noahm/CakePHP-Mixpanel.git app/Plugin/Mixpanel
    # git submodule init
    # git submodule update

Then add to your app:

    /* app/Config/bootstrap.php */
    CakePlugin::load('Mixpanel'); // if you don't already have CakePlugin::loadAll();

    /* app/Config/core.php */
    Configure::write('Mixpanel.token', 'your token here');
    // be sure to load different ones for development and production

    /* app/Controller/AppController.php */
    public $helpers = array(..., 'Mixpanel.Mixpanel', ...);
    public $components = array(..., 'Mixpanel.Mixpanel', ...);
    function beforeFilter() {
        // if a user is logged in
        $this->Mixpanel->identify($user_id);
        $this->Mixpanel->name_tag($user_name);
        $this->Mixpanel->register($superProperties);

        /* To make use of the people API */
		$this->Mixpanel->people($this->Auth->user('id'), array(
			'$username' => $this->Auth->user('username'),
			'$email' => $this->Auth->user('email'),
			'$created' => $this->Auth->user('created'),
			'$last_login' => $this->Auth->user('connected'),
			'my_custom_var' => $my_custom_var,
		));
    }

    /* app/Controller/PostController.php */
    function create() {
        if ($this->request->is('post')) {
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash('Your post has been saved.');
                $this->Mixpanel->track('Post Created', array(
                    'author' => 'Max Payne',
                    'category' => 'Code',
                ));
                $this->redirect(array('action'=>'index'));
            }
        }
    }

    /* app/View/Layouts/default.ctp (and other layouts) */
    <?php echo $this->Mixpanel->embed(); ?>
    </body>
    </html>

And you'll be sending events to mixpanel in no time!

License
-------

Copyright 2012 Noah Manneschmidt

Available for you to use under the MIT license. See: http://www.opensource.org/licenses/MIT
