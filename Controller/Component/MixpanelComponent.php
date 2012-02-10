<?php
class MixpanelComponent extends Component {
	public $components = array(
		'Session',
	);
	
	function initialize($controller) {
		$this->settings = array(
			'token' => Configure::read('Mixpanel.token'),
			'properties' => array(),
		);
		if (!$this->Session->check('Mixpanel.events')) {
			$this->Session->write('Mixpanel.events', array());
		}
	}
	
	function beforeRender() {
		Configure::write('Mixpanel.events', $this->Session->read('Mixpanel.events'));
		Configure::write('Mixpanel.settings', $this->settings);
		$this->Session->delete('Mixpanel.events');
	}
	
	function name_tag($name) {
		$this->settings['name_tag'] = $name;
	}
	
	function identify($id) {
		$this->settings['identify'] = $id;
	}
	
	function track($event, $properties = array()) {
		$events = $this->Session->read('Mixpanel.events');
		$events[] = compact('event', 'properties');
		$this->Session->write('Mixpanel.events', $events);
	}
	
	function trackInternal($event, $properties = array()) {
		$this->track($event, $properties);
		// TODO replace this with an option to send the event straight to mixpanel
		// for potential implementation see http://petewarden.typepad.com/searchbrowser/2008/06/how-to-post-an.html
	}
}
