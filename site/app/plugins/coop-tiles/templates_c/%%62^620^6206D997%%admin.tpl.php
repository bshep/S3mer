<?php /* Smarty version 2.6.19, created on 2008-04-29 21:46:12
         compiled from admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'admin.tpl', 11, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('title' => 'Tiles Administration')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div id="adminContainer1" class="adminContainer">
		<div id="headerTxt1" class="headerTxt">
			Tile Creation
		</div>
		<div id="fields" class="fields">
			<form action="newTile.php" name="newTileForm" method="post">
				<select name="TileType">
					<option value =" ">-Select Tile Type-</option>
					<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['allTypesId'],'output' => $this->_tpl_vars['allTypesName']), $this);?>

				</select>
				<select name="Category">
					<option value =" ">-Select Category-</option>
					<?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['allCatsId'],'output' => $this->_tpl_vars['allCatsName']), $this);?>

				</select>
				<textarea name="description" rows="8" cols="55" id="contentstxt"></textarea>
				<input type="text" name="url" value="" id="url"/>
				<input type="submit" name="some_name" value="Create Tile" id="some_name" />
			</form>
		</div>
	</div>

	<div id="TileList" class="tileList">
		<?php unset($this->_sections['tiles']);
$this->_sections['tiles']['name'] = 'tiles';
$this->_sections['tiles']['loop'] = is_array($_loop=$this->_tpl_vars['alltiles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['tiles']['show'] = true;
$this->_sections['tiles']['max'] = $this->_sections['tiles']['loop'];
$this->_sections['tiles']['step'] = 1;
$this->_sections['tiles']['start'] = $this->_sections['tiles']['step'] > 0 ? 0 : $this->_sections['tiles']['loop']-1;
if ($this->_sections['tiles']['show']) {
    $this->_sections['tiles']['total'] = $this->_sections['tiles']['loop'];
    if ($this->_sections['tiles']['total'] == 0)
        $this->_sections['tiles']['show'] = false;
} else
    $this->_sections['tiles']['total'] = 0;
if ($this->_sections['tiles']['show']):

            for ($this->_sections['tiles']['index'] = $this->_sections['tiles']['start'], $this->_sections['tiles']['iteration'] = 1;
                 $this->_sections['tiles']['iteration'] <= $this->_sections['tiles']['total'];
                 $this->_sections['tiles']['index'] += $this->_sections['tiles']['step'], $this->_sections['tiles']['iteration']++):
$this->_sections['tiles']['rownum'] = $this->_sections['tiles']['iteration'];
$this->_sections['tiles']['index_prev'] = $this->_sections['tiles']['index'] - $this->_sections['tiles']['step'];
$this->_sections['tiles']['index_next'] = $this->_sections['tiles']['index'] + $this->_sections['tiles']['step'];
$this->_sections['tiles']['first']      = ($this->_sections['tiles']['iteration'] == 1);
$this->_sections['tiles']['last']       = ($this->_sections['tiles']['iteration'] == $this->_sections['tiles']['total']);
?>
		<form action="deleteTile.php" name="deleteTileForm_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" method="post">
			<div id="tileRow_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileRow">
				<div id="tileType_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileType">
					<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['type']; ?>

				</div>
				<div id="tileCategory_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileCategory">
					<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['category']; ?>

				</div>
				<div id="tileContents_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileContents">
					<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['content']; ?>

				</div>
				<div id="tileURL_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileURL">
					<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>

				</div>
				<div id="tileDelete_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" class="tileDelete">
					<input type="hidden" name="idtodelete" value="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" id="idToDelete_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" />
					<input type="submit" name="delete" value="x" id="delete_<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['id']; ?>
" />
				</div>
			</div>
		</form>
		<?php endfor; endif; ?>
	</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>