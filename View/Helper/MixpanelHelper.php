<?php
App::uses('AppHelper', 'View/Helper');
class MixpanelHelper extends AppHelper {
	public $name = 'Mixpanel';
	
	function test() {
		// debug($this->_View->request);
		return '';
	}
	
	function embed() {
		$include = <<<HTML
<!-- start Mixpanel --><script type="text/javascript">var mpq=[];mpq.push(["init",TOKEN]);(function(){var b,a,e,d,c;b=document.createElement("script");b.type="text/javascript";b.async=true;b.src=(document.location.protocol==="https:"?"https:":"http:")+"//api.mixpanel.com/site_media/js/api/mixpanel.js";a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(b,a);e=function(f){return function(){mpq.push([f].concat(Array.prototype.slice.call(arguments,0)))}};d=["init","track","track_links","track_forms","register","register_once","identify","name_tag","set_config"];for(c=0;c<d.length;c++){mpq[d[c]]=e(d[c])}})();
TRACKERS
</script><!-- end Mixpanel -->
HTML;

		$settings = Configure::read('Mixpanel.settings');
		$events   = Configure::read('Mixpanel.events');
		$register = Configure::read('Mixpanel.register');
		
		$trackers = array();
		if (Configure::read('debug')) $trackers[] = 'mpq.set_config({debug: true});';
		if (isset($settings['identify'])) $trackers[] = sprintf('mpq.identify(%s);', json_encode($settings['identify']));
		if (isset($settings['name_tag'])) $trackers[] = sprintf('mpq.name_tag(%s);', json_encode($settings['name_tag']));
		if (isset($register)) $trackers[] = sprintf('mpq.registry(%s);', json_encode($register));
		
		foreach ($events as $event) {
			$properties = $event['properties'];
			$properties = array_merge($settings['properties'], $properties);
			
			$trackers[] = sprintf(
				'mpq.track(%s, %s);',
				json_encode($event['event']),
				(!empty($properties)) ? json_encode($properties) : '{}'
			);
		}
		
		return str_replace(
			array('TOKEN', 'TRACKERS'),
			array(json_encode($settings['token']), join("\n", $trackers)),
			$include
		);
	}
}
