<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

$selected = 'selected="selected"'; 	
	
JHtml::_('behavior.tooltip');

//make javascript array from categories
$javascript_array_categories = 'var categories = new Array(';
$first = true;
foreach($this->items as $category){		
	if($first){
		$first = false;
	}else{
		$javascript_array_categories .= ',';
	}
	$javascript_array_categories .= "'".$category->id."'";
}	
$javascript_array_categories .= ');';
		
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_categories."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;	
	for (i = 0; i < categories.length; i++){
		box_id = categories[i]+'__'+usergroup_id;
		hidden_id = categories[i]+'__'+usergroup_id+'__hidden';
		if(action==true){
			document.getElementById(box_id).checked = true;
			document.getElementById(hidden_id).value = hidden_id+'__1';
		}else{
			document.getElementById(box_id).checked = false;
			document.getElementById(hidden_id).value = hidden_id+'__';
		}
	}	
}

function toggle_right(hidden_field_id){
	field = document.getElementById(hidden_field_id);
	if(field.value==hidden_field_id+'__1'){
		field.value = hidden_field_id+'__';
	}else{
		field.value = hidden_field_id+'__1';
	}
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='access_categories_apply'){				
		submitform('access_categories_save');		
	}
	if (task=='access_categories_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('access_categories_save');		
	}
}


</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=categories'); ?>">	
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
	
<table id="am_subheader">
	<tr>
		<td>	
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_CATEGORIES_INFO_B'); ?>. <?php echo JText::_('COM_ACCESSMANAGER_CATEGORIES_INFO'); ?>.
			</p>
			<?php	
					
			//message if item access is not activated		
			if($this->controller->am_config['category_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_CATEGORIES_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			if(!file_exists(JPATH_ROOT.DS.'plugins'.DS.'search'.DS.'categoriesaccessmanager'.DS.'categoriesaccessmanager.php')){						
				echo '<p class="am_red">plugin \'Search - Categories Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_INSTALLED').'. <a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">'.JText::_('COM_ACCESSMANAGER_DOWNLOAD').'</a></p>';
			}
			if(!$this->plugin_search_categoriesaccessmanager_enabled){
				echo '<p class="am_red">plugin \'Search - Categories Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=enable_plugin&plugin=categoriesaccessmanager&folder=search&from=categories">'.JText::_('COM_ACCESSMANAGER_ENABLE_PLUGIN').'</a>. ';
				echo JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_A').' Access-Manager. '.JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_B').'.</p>';
			}
			if($this->plugin_search_categories_enabled){
				echo '<p class="am_red">plugin \'Search - Categories\' '.JText::_('COM_ACCESSMANAGER_IS_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=disable_plugin&plugin=categories&folder=search&from=categories">'.JText::_('COM_ACCESSMANAGER_DISABLE_PLUGIN').'</a>. ';
				echo JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_C').' Access-Manager. '.JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_D').'.';	
				echo '</p>';
			}
			$file = JPATH_ROOT.DS.'modules'.DS.'mod_roktabs'.DS.'helper.php';
			if(file_exists($file)){		
				if($this->helper->check_for_code($file, 'JPATH_SITE.\'/components/com_content/models/articles.php\')')){					
					echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_CONFLICT_WITH').' mod_roktabs. <a href="http://www.pages-and-items.com/contribute/other-stuff/fix-for-rok-modules" target="_blank">'.JText::_('COM_ACCESSMANAGER_READ_MORE').'</a></p>';
				}
			}	
			$file = JPATH_ROOT.DS.'modules'.DS.'mod_roknewspager'.DS.'lib'.DS.'helper.php';
			if(file_exists($file)){		
				if($this->helper->check_for_code($file, 'JPATH_SITE.\'/components/com_content/models/articles.php\')')){					
					echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_CONFLICT_WITH').' mod_roknewspager. <a href="http://www.pages-and-items.com/contribute/other-stuff/fix-for-rok-modules" target="_blank">'.JText::_('COM_ACCESSMANAGER_READ_MORE').'</a></p>';
				}
			}
			$file = JPATH_ROOT.DS.'modules'.DS.'mod_roknewsflash'.DS.'helper.php';
			if(file_exists($file)){		
				if($this->helper->check_for_code($file, 'JPATH_SITE.\'/components/com_content/models/articles.php\')')){					
					echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_CONFLICT_WITH').' mod_roknewsflash. <a href="http://www.pages-and-items.com/contribute/other-stuff/fix-for-rok-modules" target="_blank">'.JText::_('COM_ACCESSMANAGER_READ_MORE').'</a></p>';
				}
			}		
	
			echo JHtml::_('sliders.start','config-category-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>	
			<table class="adminlist am_table">
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_ACTIVATECATEGORIES'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="category_active" value="true" <?php if($this->controller->am_config['category_active']){echo 'checked="checked"';} ?> />
					</td>
					<td>					
						<?php echo JText::_('COM_ACCESSMANAGER_CATEGORIES_INFO_B').'. '.JText::_('COM_ACCESSMANAGER_NOACCESS_IS_HIDDEN_CATEGORIES').'.<br />'.JText::_('COM_ACCESSMANAGER_ONLY_IN_COM_CONTENT2').' (com_content) '.JText::_('COM_ACCESSMANAGER_AND').' '.JText::_('COM_ACCESSMANAGER_MODULES').':  mod_articles_categories, mod_articles_category, mod_articles_latest, mod_articles_news, mod_related_articles '.JText::_('COM_ACCESSMANAGER_AND').' mod_articles_popular.'; 
						echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';
						?>	
					</td>
				</tr>	
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="category_reverse_access" value="true" <?php if($this->controller->am_config['category_reverse_access']){echo 'checked="checked"';} ?> />
					</td>
					<td>
						<?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO_L'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO'); 
						}
						
						?>.					
					</td>
				</tr>
				<tr>		
					<td>
						<?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT'); 
						}					
						?>
					</td>
					<td>
						<label style="white-space: nowrap;"><input type="radio" name="category_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['category_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="category_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['category_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_EVERY_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP'); 
						}
						?></label>
					</td>
					<td>
						<?php 					
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT_INFO'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT_INFO'); 
						}
						?>.						
					</td>
				</tr>			
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_MESSAGETYPE_CATEGORY'); ?>
					</td>
					<td colspan="2">
						<?php echo JText::_('COM_ACCESSMANAGER_SEE_ARTICLE_ACCESS').' \''.JText::_('COM_ACCESSMANAGER_MESSAGETYPE_ITEMS').'\' '.JText::_('COM_ACCESSMANAGER_ON_PAGE'); ?> <a href="index.php?option=com_accessmanager&view=articles"><?php echo JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?></a>.					
					</td>
				</tr>			
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_A'); ?>
					</td>
					<td colspan="2">
						<?php echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_INFO_C').' (\''.JText::_('COM_ACCESSMANAGER_ITEM_ACCESS').'\', \''.JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS').'\'), '.JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_INFO_B'); ?>.<br />
						<?php echo JText::_('COM_ACCESSMANAGER_SEE_ARTICLE_ACCESS').' \''.JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_A').'\' '.JText::_('COM_ACCESSMANAGER_ON_PAGE'); ?> <a href="index.php?option=com_accessmanager&view=articles"><?php echo JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?></a>.
					</td>
				</tr>				
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
					</td>
					<td colspan="2">
						<?php			
							echo $this->helper->display_multigrouplevel_select_config('category', $this->controller->am_config);				
						?>					
					
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO_CATEGORY'); ?>.					
					</td>
				</tr>
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<?php
			
						//legend and message if reverse access	
						$this->controller->reverse_access_warning('category_reverse_access');
						
						//message in free version that these restrictions will not work in free version
						$this->controller->not_in_free_version();			
						
						?>
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area"  />			
						&nbsp;
						<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						&nbsp;
						<button onclick="document.adminForm.filter_search.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>		
						<!--
						&nbsp;
						<select name="filter_access" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
						</select>
						-->
						&nbsp;
						<select name="filter_published" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
						</select>
						&nbsp;
						<select name="filter_language" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
						</select>
					</td>
					<td class="align_right">
						<?php
							if($this->controller->am_config['based_on']=='level'){		
								echo JText::_('COM_ACCESSMANAGER_DISPLAY_LEVELS');
							}else{
								echo JText::_('COM_ACCESSMANAGER_DISPLAY_USERGROUPS');
							}
						?>:
						<br />
						<button onclick="usergroups_to_cookie();this.form.submit();"><?php echo JText::_('COM_ACCESSMANAGER_GO'); ?></button>
					</td>
				</tr>
			</table>	
		</td>	
		<td id="td_accesslevel_selector">
			<?php 			
			echo $this->controller->accesslevel_selector(); 
			?>
		</td>
	</tr>
</table>		
<table class="adminlist">
	<tr>		
		<th style="text-align: center;">
			id			
		</th>	
		<th align="left">				
			<?php 
			$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
			echo JHTML::_('grid.sort', $label, 'c.title', $listDirn, $listOrder); 			
			?>	
			&nbsp;	
			<?php 
			$label = ucfirst(JText::_('JFIELD_ORDERING_LABEL')).' '; 			
			echo JHTML::_('grid.sort', $label, 'c.lft', $listDirn, $listOrder); 			
			?>
		</th>
		<?php			
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>		
		
	</tr>
		
	<?php
							
		$k = 1;		
		
		//row with select_all checkboxes
		echo '<tr class="row1">';
		echo '<td>&nbsp;</td>';
		echo '<td class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';		
		foreach($this->am_grouplevels as $am_accesslevel){
			echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
		}
		echo '</tr>';
		
		$counter = 0;	
		foreach($this->items as $category){	
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}				
			echo '<tr class="row'.$k.'">';
			echo '<td class="column_ids">'.$category->id.'</td>';
			//echo '<td style="padding-left: '.($category->level*2).'0px">';	
			$has_superscript = '';
			if($category->published=='0'){
				$has_superscript = ' has_superscript';
			}
			echo '<td class="indent-'.(intval(($category->level-1)*15)+4).$has_superscript.'">';				
			if($category->title!=''){
				echo $category->title;
			}else{
				echo $category->name;
			}	
			if($category->published=='0'){
				echo '<sup class="am_superscript">1</sup>';
			}		
			echo '</td>';					
			foreach($this->am_grouplevels as $am_accesslevel){
				$checked = '';
				$checked_hidden = '';
				if (in_array($category->id.'__'.$am_accesslevel->id, $this->access_categories)) {
					$checked = 'checked="checked"';
					$checked_hidden = '1';
				}
				echo '<td style="text-align:center;"><input type="hidden" name="category_access_hidden[]" id="'.$category->id.'__'.$am_accesslevel->id.'__hidden" value="'.$category->id.'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="category_access[]" id="'.$category->id.'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$category->id.'__'.$am_accesslevel->id.'__hidden\');" value="'.$category->id.'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
			}
			echo '</tr>';			
			if($counter==7){
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';					
				$this->controller->loop_accesslevels($this->am_grouplevels);
				echo '</tr>';
				$counter = 0;
			}
			$counter = $counter+1;						
		}	
	?>			
</table>
<table class="adminlist">
	<tfoot>
		<tr>
			<td>
			<?php 
				echo $this->pagination->getListFooter();
			?>
			</td>
		</tr>
	</tfoot>
</table>
<table>
	<tr>
		<td class="am_red">1
		</td>
		<td>=
		</td>
		<td><?php echo JText::_('COM_ACCESSMANAGER_NOT_PUBLISHED_B'); ?>.
		</td>
	</tr>	
</table>
</form>
<?php

$this->controller->display_footer();

?>