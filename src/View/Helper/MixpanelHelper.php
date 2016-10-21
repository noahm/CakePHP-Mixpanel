<?php

namespace CakeMixpanel\View\Helper;

use Cake\View\Helper;

class MixpanelHelper extends Helper {
	public $name = 'Mixpanel';
	
	function test() {
		// debug($this->_View->request);
		return '';
	}
	
	function embed() {
		$include = <<<HTML
<!-- start Mixpanel --><script type="text/javascript">(function(c,a){window.mixpanel=a;var b,d,h,e;b=c.createElement("script");b.type="text/javascript";b.async=!0;b.src=("https:"===c.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.1.min.js';d=c.getElementsByTagName("script")[0];d.parentNode.insertBefore(b,d);a._i=[];a.init=function(b,c,f){function d(a,b){var c=b.split(".");2==c.length&&(a=a[c[0]],b=c[1]);a[b]=function(){a.push([b].concat(Array.prototype.slice.call(arguments,0)))}}var g=a;"undefined"!==typeof f?
g=a[f]=[]:f="mixpanel";g.people=g.people||[];h="disable track track_pageview track_links track_forms register register_once unregister identify name_tag set_config people.identify people.set people.increment".split(" ");for(e=0;e<h.length;e++)d(g,h[e]);a._i.push([b,c,f])};a.__SV=1.1})(document,window.mixpanel||[]);
mixpanel.init(TOKEN);
TRACKERS</script><!-- end Mixpanel -->
HTML;

		$settings = Configure::read('Mixpanel.settings');
		$events   = Configure::read('Mixpanel.events');
		$register = Configure::read('Mixpanel.register');
		
		$trackers = array();
		# Integration
		if (Configure::read('debug')) $trackers[] = 'mixpanel.set_config({debug: true});';
		if (isset($settings['identify'])) $trackers[] = sprintf('mixpanel.identify(%s);', json_encode($settings['identify']));
		if (isset($settings['name_tag'])) $trackers[] = sprintf('mixpanel.name_tag(%s);', json_encode($settings['name_tag']));
		if (isset($register)) $trackers[] = sprintf('mixpanel.register(%s);', json_encode($register));

		if (!empty($events)) {
			foreach ($events as $event) {
				$properties = $event['properties'];
				$properties = array_merge($settings['properties'], $properties);

				$trackers[] = sprintf(
					'mixpanel.track(%s, %s);',
					json_encode($event['event']),
					(!empty($properties)) ? json_encode($properties) : '{}'
				);
			}
		}

		#People
		if (isset($settings['people'])) {
			 $trackers[] = sprintf('mixpanel.people.identify(%s);', json_encode($settings['people']['identify']));
			 $trackers[] = sprintf('mixpanel.people.set(%s);', json_encode($settings['people']['set']));
		}
		
		return str_replace(
			array('TOKEN', 'TRACKERS'),
			array(json_encode($settings['token']), join("\n", $trackers)),
			$include
		);
	}
}
