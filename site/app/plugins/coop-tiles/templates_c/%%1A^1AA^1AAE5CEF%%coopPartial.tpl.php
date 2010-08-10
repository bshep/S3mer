<?php /* Smarty version 2.6.19, created on 2008-07-02 16:34:18
         compiled from coopPartial.tpl */ ?>
<ul>
	<?php unset($this->_sections['acts']);
$this->_sections['acts']['name'] = 'acts';
$this->_sections['acts']['loop'] = is_array($_loop=$this->_tpl_vars['allacts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['acts']['show'] = true;
$this->_sections['acts']['max'] = $this->_sections['acts']['loop'];
$this->_sections['acts']['step'] = 1;
$this->_sections['acts']['start'] = $this->_sections['acts']['step'] > 0 ? 0 : $this->_sections['acts']['loop']-1;
if ($this->_sections['acts']['show']) {
    $this->_sections['acts']['total'] = $this->_sections['acts']['loop'];
    if ($this->_sections['acts']['total'] == 0)
        $this->_sections['acts']['show'] = false;
} else
    $this->_sections['acts']['total'] = 0;
if ($this->_sections['acts']['show']):

            for ($this->_sections['acts']['index'] = $this->_sections['acts']['start'], $this->_sections['acts']['iteration'] = 1;
                 $this->_sections['acts']['iteration'] <= $this->_sections['acts']['total'];
                 $this->_sections['acts']['index'] += $this->_sections['acts']['step'], $this->_sections['acts']['iteration']++):
$this->_sections['acts']['rownum'] = $this->_sections['acts']['iteration'];
$this->_sections['acts']['index_prev'] = $this->_sections['acts']['index'] - $this->_sections['acts']['step'];
$this->_sections['acts']['index_next'] = $this->_sections['acts']['index'] + $this->_sections['acts']['step'];
$this->_sections['acts']['first']      = ($this->_sections['acts']['iteration'] == 1);
$this->_sections['acts']['last']       = ($this->_sections['acts']['iteration'] == $this->_sections['acts']['total']);
?>
	<li><span class="hora"><?php echo $this->_tpl_vars['allacts'][$this->_sections['acts']['index']]['hora']; ?>
</span><?php echo $this->_tpl_vars['allacts'][$this->_sections['acts']['index']]['actividad']; ?>
</li>
	<?php endfor; endif; ?>
</ul>