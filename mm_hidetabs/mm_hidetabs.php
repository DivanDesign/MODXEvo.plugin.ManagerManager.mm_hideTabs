<?php
/**
 * mm_hideTabs
 * @version 1.1 (2012-11-13)
 * 
 * @desc A widget for ManagerManager plugin that allows one or a few default tabs to be hidden on the document edit page.
 * 
 * @uses ManagerManager plugin 0.6.2.
 * 
 * @param $tabs {'general'; 'settings'; 'access'} - The id(s) of the tab(s) this should apply to. @required
 * @param $roles {comma separated string} - The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles).
 * @param $templates {comma separated string} - Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates).
 * 
 * @link http://code.divandesign.biz/modx/mm_hidetabs/1.1
 * 
 * @copyright 2012
 */

function mm_hideTabs($tabs, $roles = '', $templates = ''){
	global $modx;
	$e = &$modx->Event;
	
	// if we've been supplied with a string, convert it into an array
	$tabs = makeArray($tabs);
	
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if ($e->name == 'OnDocFormRender' && useThisRule($roles, $templates)){
		$output = "//  -------------- mm_hideTabs :: Begin ------------- \n";
		
		foreach($tabs as $tab){
			$tabId = prepareTabId($tab);
			
			switch ($tab){
				case 'general':
					$output .= '$j("div#documentPane h2:nth-child(1)").hide(); ' . "\n";
					$output .= '$j("#'.$tabId.'").hide();';
				break;
				
				case 'settings':
					$output .= '$j("div#documentPane h2:nth-child(2)").hide(); ' . "\n";
					$output .= '$j("#'.$tabId.'").hide();';
				break;
				
				// =< v1.0.0 only
				case 'meta':
					if($modx->hasPermission('edit_doc_metatags') && $modx->config['show_meta'] != "0"){
						$output .= '$j("div#documentPane h2:nth-child(3)").hide(); ' . "\n";
						$output .= '$j("#'.$tabId.'").hide();';
					}
				break;
				
				// Meta tags tab is removed by default in version 1.0.1+ but can still be enabled via a preference.
				// Access tab was only added in 1.0.1
				// Since counting the tabs is the only way of determining this, we need to know if this has been activated
				// If meta tabs are active, the "access" tab is index 4 in the HTML; otherwise index 3.
				// If config['show_meta'] is NULL, this is a version before this option existed, e.g. < 1.0.1
				// For versions => 1.0.1, 0 is the default value to not show them, 1 is the option to show them.
				case 'access':
					$access_index = ($modx->config['show_meta'] == "0") ? 3 : 4;
					$output .= '$j("div#documentPane h2:nth-child('.$access_index.')").hide(); ' . "\n";
					$output .= '$j("#'.$tabId.'").hide();';
				break;
			}
			
		}
		
		$output .=
'
//All tabs
var $mm_hideTabs_allTabs = $j();

for (var i = 0; i < tpSettings.pages.length - 1; i++){
	$mm_hideTabs_allTabs = $mm_hideTabs_allTabs.add(tpSettings.pages[i].tab);
}

//If the active tab is hidden
if ($j(tpSettings.pages[tpSettings.getSelectedIndex()].tab).is(":hidden")){
	//Activate the first visible tab
	$mm_hideTabs_allTabs.filter(":visible").eq(0).trigger("click");
}
';
		
		$output .= "//  -------------- mm_hideTabs :: End ------------- \n";
		
		$e->output($output . "\n");
	}
}
?>