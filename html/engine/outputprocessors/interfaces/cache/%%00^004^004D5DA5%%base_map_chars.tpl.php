<?php /* Smarty version 2.6.16, created on 2008-03-28 16:36:15
         compiled from base_map_chars.tpl */ ?>
<div style="height: 120px; overflow: auto">
<ul>
<?php unset($this->_sections['model']);
$this->_sections['model']['name'] = 'model';
$this->_sections['model']['loop'] = is_array($_loop=$this->_tpl_vars['models']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['model']['show'] = true;
$this->_sections['model']['max'] = $this->_sections['model']['loop'];
$this->_sections['model']['step'] = 1;
$this->_sections['model']['start'] = $this->_sections['model']['step'] > 0 ? 0 : $this->_sections['model']['loop']-1;
if ($this->_sections['model']['show']) {
    $this->_sections['model']['total'] = $this->_sections['model']['loop'];
    if ($this->_sections['model']['total'] == 0)
        $this->_sections['model']['show'] = false;
} else
    $this->_sections['model']['total'] = 0;
if ($this->_sections['model']['show']):

            for ($this->_sections['model']['index'] = $this->_sections['model']['start'], $this->_sections['model']['iteration'] = 1;
                 $this->_sections['model']['iteration'] <= $this->_sections['model']['total'];
                 $this->_sections['model']['index'] += $this->_sections['model']['step'], $this->_sections['model']['iteration']++):
$this->_sections['model']['rownum'] = $this->_sections['model']['iteration'];
$this->_sections['model']['index_prev'] = $this->_sections['model']['index'] - $this->_sections['model']['step'];
$this->_sections['model']['index_next'] = $this->_sections['model']['index'] + $this->_sections['model']['step'];
$this->_sections['model']['first']      = ($this->_sections['model']['iteration'] == 1);
$this->_sections['model']['last']       = ($this->_sections['model']['iteration'] == $this->_sections['model']['total']);
?>
<li><a href="javascript:swchar('<?php echo $this->_tpl_vars['models'][$this->_sections['model']['index']]; ?>
');"><?php echo $this->_tpl_vars['models'][$this->_sections['model']['index']]; ?>
</a></li>
<?php endfor; endif; ?>
</ul>
</div>