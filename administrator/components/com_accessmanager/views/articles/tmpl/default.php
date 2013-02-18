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

//make javascript array from items
$javascript_array_articles = 'var items = new Array(';
$first = true;
foreach($this->items as $item){		
	if($first){
		$first = false;
	}else{
		$javascript_array_articles .= ',';
	}
	$javascript_array_articles .= "'".$item->id."'";
}	
$javascript_array_articles .= ');';
		
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_articles."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;	
	for (i = 0; i < items.length; i++){
		box_id = items[i]+'__'+usergroup_id;
		hidden_id = items[i]+'__'+usergroup_id+'__hidden';
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
	if (task=='access_articles_apply'){				
		submitform('access_articles_save');		
	}
	if (task=='access_articles_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('access_articles_save');		
	}
}

</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=articles'); ?>">	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />	
	<?php echo JHTML::_( 'form.token' ); ?>		
			
	
<table id="am_subheader">
	<tr>
		<td>
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_ITEMS_INFO'); ?>.
			</p>
			<?php
			//message if item access is not activated		
			if($this->controller->am_config['article_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_ACTIVE_ITEMS').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}
			
			if(!file_exists(JPATH_ROOT.DS.'plugins'.DS.'search'.DS.'contentaccessmanager'.DS.'contentaccessmanager.php')){						
				echo '<p class="am_red">plugin \'Search - Content Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_INSTALLED').'. <a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">'.JText::_('COM_ACCESSMANAGER_DOWNLOAD').'</a></p>';
			}
			if(!$this->plugin_search_contentaccessmanager_enabled){
				echo '<p class="am_red">plugin \'Search - Content Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=enable_plugin&plugin=contentaccessmanager&folder=search&from=articles">'.JText::_('COM_ACCESSMANAGER_ENABLE_PLUGIN').'</a>. ';
				echo JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_A').' Access-Manager. '.JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_B').'.</p>';
			}
			if($this->plugin_search_content_enabled){
				echo '<p class="am_red">plugin \'Search - Content\' '.JText::_('COM_ACCESSMANAGER_IS_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=disable_plugin&plugin=content&folder=search&from=articles">'.JText::_('COM_ACCESSMANAGER_DISABLE_PLUGIN').'</a>. ';
				echo JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_C').' Access-Manager. '.JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_D').'.';	
				echo '</p>';
			}
			if(!file_exists(JPATH_ROOT.DS.'plugins'.DS.'search'.DS.'categoriesaccessmanager'.DS.'categoriesaccessmanager.php')){						
				echo '<p class="am_red">plugin \'Search - Categories Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_INSTALLED').'. <a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">'.JText::_('COM_ACCESSMANAGER_DOWNLOAD').'</a></p>';
			}
			if(!$this->plugin_search_categoriesaccessmanager_enabled){
				echo '<p class="am_red">plugin \'Search - Categories Access manager\' '.JText::_('COM_ACCESSMANAGER_IS_NOT_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=enable_plugin&plugin=categoriesaccessmanager&folder=search&from=articles">'.JText::_('COM_ACCESSMANAGER_ENABLE_PLUGIN').'</a>. ';
				echo JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_A').' Access-Manager. '.JText::_('COM_ACCESSMANAGER_SEARCH_PLUGIN_INFO_B').'.</p>';
			}
			if($this->plugin_search_categories_enabled){
				echo '<p class="am_red">plugin \'Search - Categories\' '.JText::_('COM_ACCESSMANAGER_IS_ENABLED');
				echo '. <a href="index.php?option=com_accessmanager&task=disable_plugin&plugin=categories&folder=search&from=articles">'.JText::_('COM_ACCESSMANAGER_DISABLE_PLUGIN').'</a>. ';
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
				
			
			echo JHtml::_('sliders.start','config-article-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>	
			<table class="adminlist am_table">					
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_ITEMS_ACTIVATE'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="article_active" value="true" <?php if($this->controller->am_config['article_active']){echo 'checked="checked"';} ?> />
					</td>
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_ITEMS_INFO').'. '.JText::_('COM_ACCESSMANAGER_NOACCESS_IS_HIDDEN').'.<br />'.JText::_('COM_ACCESSMANAGER_ONLY_IN_COM_CONTENT2').' (com_content) '.JText::_('COM_ACCESSMANAGER_AND').' '.JText::_('COM_ACCESSMANAGER_MODULES').':   mod_articles_category, mod_articles_latest, mod_articles_news, mod_related_items '.JText::_('COM_ACCESSMANAGER_AND').' mod_articles_popular.'; 
						echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';
						?>					
					</td>
				</tr>	
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="article_reverse_access" value="true" <?php if($this->controller->am_config['article_reverse_access']){echo 'checked="checked"';} ?> />
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
						<label style="white-space: nowrap;"><input type="radio" name="article_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['article_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> />
						<?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?>
						</label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="article_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['article_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> />
						<?php 					
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_EVERY_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP'); 
						}
						?>
						</label>
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
						<?php echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_A'); ?>
					</td>
					<td colspan="2">					
						<?php					
						echo JText::_('COM_ACCESSMANAGER_ARTICLE_AND_CATEGORY');
						?>.
						<br />
						<label><input type="radio" name="content_access_together" value="every_group" class="radio" <?php if($this->controller->am_config['content_access_together']=='every_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_EVERY_LEVEL').' ('.$this->controller->am_strtolower(JText::_('JDEFAULT')).' '.JText::_('COM_ACCESSMANAGER_IN').' Joomla).';  
						}else{
							echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_EVERY_GROUP').' ('.JText::_('JDEFAULT').' '.JText::_('COM_ACCESSMANAGER_IN').' Joomla).'; 
						}
						?></label><br />					
						<label><input type="radio" name="content_access_together" value="one_group" class="radio" <?php if($this->controller->am_config['content_access_together']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_ONE_LEVEL');
						}else{
							echo JText::_('COM_ACCESSMANAGER_CONTENT_ACCESS_TOGETHER_ONE_GROUP'); 
						}
						?></label>
					</td>
				</tr>					
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_MESSAGETYPE_ITEMS'); ?><br />(option=com_content&amp;view=article)
					</td>
					<td colspan="2">					
						<label><input type="radio" name="items_message_type" value="alert" class="radio" <?php if($this->controller->am_config['items_message_type']=='alert'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_MESSAGE_TYPE_ALERT'); ?></label><br />					
						<label><input type="radio" name="items_message_type" value="only_text" class="radio" <?php if($this->controller->am_config['items_message_type']=='only_text'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_MESSAGE_TYPE_ONLY_TEXT'); ?></label><br />
						<label><input type="radio" name="items_message_type" value="redirect" class="radio" <?php if($this->controller->am_config['items_message_type']=='redirect'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_REDIRECT_TO_URL'); ?>:</label>
						<?php
						$no_item_access_full_url = '';
						if($this->controller->am_config['no_item_access_full_url']){
							$no_item_access_full_url = $this->controller->am_config['no_item_access_full_url'];
						}
						?>
						<input type="text" name="no_item_access_full_url" class="long_text_field" value="<?php echo $no_item_access_full_url; ?>" />
						<br />
						<label><input type="radio" name="items_message_type" value="login" class="radio" <?php if($this->controller->am_config['items_message_type']=='login'){echo 'checked="checked"';} ?> /><?php echo $this->controller->am_strtolower(JText::_('JLOGIN')); ?></label>
					</td>
				</tr>				
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_TRUNCATE_ARTICLE_TITLE'); ?>
					</td>
					<td>
						<?php 
						
										
						echo '<select name="truncate_article_title">';
						echo '<option value="">'.JText::_('COM_ACCESSMANAGER_NONE').'</option>';
						$truncate_array = array(30, 40, 50, 60, 70, 80, 100);
						foreach($truncate_array as $truncate_number){		
							echo '<option value="'.$truncate_number.'"';
							if($this->controller->am_config['truncate_article_title']==$truncate_number){
								echo $selected;
							}				
							echo '>'.$truncate_number.'</option>';						
						}
						echo '</select>';
						
						?>					
					</td>
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_TRUNCATE_ARTICLE_TITLE_INFO'); ?>.					
					</td>
				</tr>	
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DISPLAY_CATEGORY_COLUMN'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="display_category_column_article" value="true" <?php if($this->controller->am_config['display_category_column_article']){echo 'checked="checked"';} ?> />	
					</td>
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DISPLAY_CATEGORY_COLUMN_INFO'); ?>.					
					</td>
				</tr>					
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
					</td>
					<td colspan="2">
						<?php			
							echo $this->helper->display_multigrouplevel_select_config('article', $this->controller->am_config);				
						?>					
					
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO_ARTICLE'); ?>.					
					</td>
				</tr>							
			</table>
			<?php echo JHtml::_('sliders.end'); ?>				
			<table style="width: 100%;">
				<tr>
					<td>
					<?php

					//legend and message if reverse access	
					$this->controller->reverse_access_warning('article_reverse_access');
								
					?>
					<input type="text" name="items_search" id="items_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area"  />			
					&nbsp;
					<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					&nbsp;
					<button onclick="document.getElementById('items_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					&nbsp;
					<!--
					<select name="filter_access" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
					</select>
					&nbsp;
					-->
					<select name="filter_published" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
					</select>
					&nbsp;
					<select name="items_category_filter" onchange="this.form.submit()" class="inputbox">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category'));?>
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
		</th>			
			<?php
			if($this->controller->am_config['display_category_column_article']){
			?>
				<th align="left" class="nowrap">
					<?php 			
					$label = ucfirst(JText::_('COM_ACCESSMANAGER_CATEGORY')).' '; 			
					echo JHTML::_('grid.sort', $label, 'd.title', $listDirn, $listOrder); 			
					?>
				</th>						
			<?php
			}					
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>
	</tr>
		
	<?php
							
		$k = 1;		
		
		//row with select_all checkboxes
		echo '<tr class="row0">';
		echo '<td>&nbsp;</td>';
		echo '<td class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';	
		if($this->controller->am_config['display_category_column_article']){
			echo '<td>&nbsp;</td>';
		}	
		foreach($this->am_grouplevels as $am_accesslevel){
			echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
		}		
		echo '</tr>';			
		
		$counter = 0;			
		foreach($this->items as $item){						
			//echo '<tr class="row'.$k.'"><td>'.$item->title;
			echo '<tr class="row'.$k.'">';
			echo '<td class="column_ids">'.$item->id.'</td>';			
			echo '<td';
			if($item->state=='0'){
				echo ' class="has_superscript"';
			}
			echo '>';
			if($this->controller->am_config['truncate_article_title']!=''){
				echo $this->controller->truncate_string($item->title, $this->controller->am_config['truncate_article_title']);
			}else{
				echo $item->title;
			}
			if($item->state=='0'){
				echo '<sup class="am_superscript">1</sup>';
			}
			echo '</td>';
			if($this->controller->am_config['display_category_column_article']){
				echo '<td>'.$item->categorytitle.'</td>';
			}					
			foreach($this->am_grouplevels as $am_accesslevel){
				$checked = '';
				$checked_hidden = '';
				if (in_array($item->id.'__'.$am_accesslevel->id, $this->access_articles)) {
					$checked = 'checked="checked"';
					$checked_hidden = '1';
				}
				echo '<td style="text-align:center;"><input type="hidden" name="item_access_hidden[]" id="'.$item->id.'__'.$am_accesslevel->id.'__hidden" value="'.$item->id.'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="item_access[]" id="'.$item->id.'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$item->id.'__'.$am_accesslevel->id.'__hidden\');" value="'.$item->id.'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
			}
			echo '</tr>';
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}	
			if($counter==7){
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';
				if($this->controller->am_config['display_category_column_article']){
					echo '<th>&nbsp;</th>';
				}				
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