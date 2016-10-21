<?php

namespace CakeMixpanel\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class MixpanelComponent extends Component {
	public $components = array(
		'Session',
	);
	
	public function initialize(Controller $controller) {
		$this->settings = array(
			'token' => Configure::read('Mixpanel.token'),
			'properties' => array(),
		);
		if (!$this->Session->check('Mixpanel.events')) {
			$this->Session->write('Mixpanel.events', array());
		}
	}
	
	public function beforeRender(Controller $controller) {
		Configure::write('Mixpanel.events', $this->Session->read('Mixpanel.events'));
		Configure::write('Mixpanel.register', $this->Session->read('Mixpanel.register'));
		Configure::write('Mixpanel.settings', $this->settings);
		$this->Session->delete('Mixpanel.events');
		$this->Session->delete('Mixpanel.register');
	}
	
	public function name_tag($name) {
		$this->settings['name_tag'] = $name;
	}
	
	public function identify($id) {
		$this->settings['identify'] = $id;
	}
	
	public function people($id, $properties = array()) {
		$this->settings['people']['identify'] = $id;
		$this->settings['people']['set'] = $properties;
	}

/**
 * Register new properties using mixpanel.register(), accepts a key => value array of properties
 * Sending a key => value with a duplicate key replaces the old value
 *
 * @param array $properties Array of key => value properties to register
 * @return void
 * @author David Kullmann
 */
	public function register($properties) {
		$register = $this->Session->read('Mixpanel.register');
		if (!empty($properties)) {
			foreach($properties as $key => $value) {
				$register[$key] = $value;
			}	
		}
		$this->Session->write('Mixpanel.register', $register);
	}
	
	public function track($event, $properties = array()) {
		$events = $this->Session->read('Mixpanel.events');
		$events[] = compact('event', 'properties');
		$this->Session->write('Mixpanel.events', $events);
	}
	
	public function trackInternal($event, $properties = array()) {
		$this->track($event, $properties);
		// TODO replace this with an option to send the event straight to mixpanel
		// for potential implementation see http://petewarden.typepad.com/searchbrowser/2008/06/how-to-post-an.html
	}
}
