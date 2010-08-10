<?php /* Smarty version 2.6.19, created on 2008-07-03 02:08:01
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'index.tpl', 7, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('title' => 'Tiles2 Index Page')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<div id="container" class="container">
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
				<div id="tile" class="tile">
					<?php if ($this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['type'] == 1): ?>
						<div id="tile" class="tile <?php echo smarty_function_cycle(array('values' => "color1,color2,color3,color4,color5,color6,color7"), $this);?>
">
							<div id="txtContainerTxt" class="txtContainerTxt">
								<span class="category"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['category']; ?>
</span><br />
								<span class="txt"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['content']; ?>
</span>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['type'] == 2): ?>
						<div id="pic" class="pic">
							<img src="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>
" />
						</div>
						<div id="txtContainerPhoto" class="txtContainerPhoto">
							<span class="category"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['category']; ?>
</span><br />
							<span class="txt"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['content']; ?>
</span>
						</div>
					<?php endif; ?>
					<?php if ($this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['type'] == 4): ?>
						<div id="video" class="video">
							<object width="200" height="200"><param name="movie" value="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>
&autoplay=1&loop=1"></param><param name="wmode" value="transparent"></param><embed src="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>
&autoplay=1&loop=1" type="application/x-shockwave-flash" wmode="transparent" width="200" height="200"></embed></object>
						</div>
						<div id="txtContainerVid" class="txtContainerVid">
							<span class="category"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['category']; ?>
</span>
						</div>
					<?php endif; ?>
					<?php if ($this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['type'] == 5): ?>
						<div id="video" class="video">
							<object type="application/x-shockwave-flash" width="200" height="200" data="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>
&autoplay=1&loop=1"><param name="quality" value="best" />	<param name="allowfullscreen" value="true" /><param name="scale" value="showAll" />	<param name="movie" value="<?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['url']; ?>
&autoplay=1&loop=1" /></object>
						</div>
						<div id="txtContainerVid" class="txtContainerVid">
							<span class="category"><?php echo $this->_tpl_vars['alltiles'][$this->_sections['tiles']['index']]['category']; ?>
</span>
						</div>
					<?php endif; ?>
				</div>
			<?php endfor; endif; ?>
		</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>