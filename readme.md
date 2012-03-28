CakePHP Mixpanel Plugin
=======================

You've got Cake, now you want Mixpanel. You read their website and they sent you here:

https://github.com/felixge/cakephp-mixpanel

But that repo hasn't been updated in years, so I made you this one! (For CakePHP 2.0)

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

Copyright 2012  Noah Manneschmidt

Available for you to use under the MIT license. See: http://www.opensource.org/licenses/MIT