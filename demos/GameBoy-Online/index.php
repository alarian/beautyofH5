<?php
define('CSSDIR', 'css');
define('JSDIR', 'js');
require_once('./res/framework.php');
class GameBoy extends site {
	function start_processing() {
		$this->title = 'GameBoy Online';
		$this->script = '
DEBUG_MESSAGES = true;
DEBUG_WINDOWING = false;
';
		$this->script_alt = array(
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/windowStack.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/terminal.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/gui.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/base64.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/transportHandler.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/json2.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/swfobject.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/resampler.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/XAudioServer.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/other/resize.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/GameBoyCore.js'),
			$this->server->convert_out_of_set_chars($this->server->url['folder'].JSDIR.'/GameBoyIO.js')
		);
		//$this->meta = array('viewport'=>'width=device-width, height=device-height');
		$this->style = '@import url("'.$this->server->convert_out_of_set_chars($this->server->url['folder'].CSSDIR.'/GameBoy.css.php').(($this->server->get('border-radius') == 'true') ? '?rounded=true' : '').'");';
		$this->manifest = $this->server->convert_out_of_set_chars($this->server->url['folder'].'gameboy.manifest.php');
	}
	function body_render() {
		//Generate the "windowing":
		$this->emulatorMain();
		$this->displayTerminal();
		$this->displayAbout();
		$this->displaySettings();
		$this->displayInstructions();
		$this->fileInput();
		//Generate the Pop-Ups:
		$this->generatePopUps();
		//Fullscreen canvas:
		$this->fullscreenGenerate();
		//DOM ready state queue:
		$this->startElement('script');
		$this->writeAttribute('type', 'text/javascript');
		$this->text('
try {
	addEvent("DOMContentLoaded", document, windowingPreInitUnsafe);
	addEvent("readystatechange", document, windowingPreInitSafe);
}
catch (error) {
	alert("Could not initialize the emulator properly. Please try using a standards compliant browser.");
}
');
		$this->endElement();
	}
	protected function emulatorMain() {
		$this->startElement('div');
		$this->writeAttribute('id', 'GameBoy');
		$this->writeAttribute('class', 'window');
		$this->startElement('div');
		$this->writeAttribute('class', 'menubar');
		$this->startElement('span');
		$this->writeAttribute('id', 'GameBoy_file_menu');
		$this->text('File');
		$this->endElement();
		$this->startElement('span');
		$this->writeAttribute('id', 'GameBoy_settings_menu');
		$this->text('Settings');
		$this->endElement();
		$this->startElement('span');
		$this->writeAttribute('id', 'GameBoy_view_menu');
		$this->text('View');
		$this->endElement();
		$this->startElement('span');
		$this->writeAttribute('id', 'GameBoy_about_menu');
		$this->text('About');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('id', 'gfx');
		$this->startElement('canvas');
		$this->writeAttribute('id', 'mainCanvas');
		$this->endElement();
		$this->startElement('span');
		$this->writeAttribute('id', 'title');
		$this->text('GameBoy');
		$this->endElement();
		$this->startElement('span');
		$this->writeAttribute('id', 'port_title');
		$this->text('Online');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function displayTerminal() {
		$this->startElement('div');
		$this->writeAttribute('id', 'terminal');
		$this->writeAttribute('class', 'window');
		$this->startElement('div');
		$this->writeAttribute('id', 'terminal_output');
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'button_rack');
		$this->startElement('button');
		$this->writeAttribute('id', 'terminal_clear_button');
		$this->writeAttribute('class', 'left');
		$this->text('Clear Messages');
		$this->endElement();
		$this->startElement('button');
		$this->writeAttribute('id', 'terminal_close_button');
		$this->writeAttribute('class', 'right');
		$this->text('Close Terminal');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function displayAbout() {
		$this->startElement('div');
		$this->writeAttribute('id', 'about');
		$this->writeAttribute('class', 'window');
		$this->startElement('div');
		$this->writeAttribute('id', 'about_message');
		$this->startElement('h1');
		$this->text('GameBoy Online');
		$this->endElement();
		$this->startElement('p');
		$this->text('This is a GameBoy Color emulator written purely in JavaScript by Grant Galitz.');
		$this->endElement();
		$this->startElement('p');
		$this->text('The graphics out is done through HTML5 canvas, with the putImageData function.');
		$this->endElement();
		$this->startElement('p');
		$this->text('Save states are implemented through the window.localStorage object, and are serialized/deserialized through JSON.');
		$this->text(' SRAM saving is also implemented through the window.localStorage object, and are serialized/deserialized through JSON.');
		$this->text(' In order for save states to work properly on most browsers, you need set the maximum size limit for DOM storage higher, to meet the needs of the emulator\'s save data size.');
		$this->endElement();
		$this->startElement('p');
		$this->text('For more information about this emulator and its source code, visit the GIT repository at: ');
		$this->startElement('a');
		$this->writeAttribute('href', 'https://github.com/grantgalitz/GameBoy-Online');
		$this->writeAttribute('target', '_blank');
		$this->text('https://github.com/grantgalitz/GameBoy-Online');
		$this->endElement();
		$this->text('.');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'button_rack');
		$this->startElement('button');
		$this->writeAttribute('id', 'about_close_button');
		$this->writeAttribute('class', 'center');
		$this->text('Close Popup');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function displaySettings() {
		$this->startElement('div');
		$this->writeAttribute('class', 'window');
		$this->writeAttribute('id', 'settings');
		$this->startElement('div');
		$this->writeAttribute('id', 'toggle_settings');
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Enable Sound:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'enable_sound');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Force Mono Sound:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'enable_mono_sound');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('GB mode has priority over GBC mode:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'disable_colors');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Use the BIOS ROM:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'enable_gbc_bios');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Auto frame skip:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'auto_frameskip');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Override ROM only cartridge typing to MBC1:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'rom_only_override');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Always allow reading and writing to the MBC banks:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'mbc_enable_override');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Colorize Classic GameBoy Palettes:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'enable_colorization');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Minimal view on fullscreen:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('checked', 'checked');
		$this->writeAttribute('id', 'do_minimal');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Resize canvas directly in JavaScript:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'software_resizing');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Disallow typed arrays to be used:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'typed_arrays_disallow');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'setting');
		$this->startElement('span');
		$this->text('Use the DMG boot ROM instead of CGB:');
		$this->endElement();
		$this->startElement('input');
		$this->writeAttribute('type', 'checkbox');
		$this->writeAttribute('id', 'gb_boot_rom_utilized');
		$this->endElement();
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'button_rack');
		$this->startElement('button');
		$this->writeAttribute('id', 'settings_close_button');
		$this->writeAttribute('class', 'center');
		$this->text('Close Settings');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function generatePopUps() {
		$this->startElement('ul');
		$this->writeAttribute('class', 'menu');
		$this->writeAttribute('id', 'GameBoy_file_popup');
		$this->startElement('li');
		$this->text('Open As');
		$this->startElement('ul');
		$this->writeAttribute('class', 'menu');
		$this->startElement('li');
		$this->writeAttribute('id', 'data_uri_clicker');
		$this->text('Base 64 Encoding');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'external_file_clicker');
		$this->text('URL Address');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'internal_file_clicker');
		$this->text('Local File');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'open_saved_clicker');
		$this->text('Saved State');
		$this->startElement('ul');
		$this->writeAttribute('id', 'save_states');
		$this->writeAttribute('class', 'menu');
		$this->endElement();
		$this->endElement();
		$this->endElement();
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'save_SRAM_state_clicker');
		$this->text('Save Game Memory');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'save_state_clicker');
		$this->text('Save Freeze State');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'set_speed');
		$this->text('Set Speed Multiplier');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'restart_cpu_clicker');
		$this->text('Restart');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'run_cpu_clicker');
		$this->text('Resume');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'kill_cpu_clicker');
		$this->text('Pause');
		$this->endElement();
		$this->endElement();
		$this->startElement('ul');
		$this->writeAttribute('class', 'menu');
		$this->writeAttribute('id', 'GameBoy_view_popup');
		$this->startElement('li');
		$this->writeAttribute('id', 'view_terminal');
		$this->text('Terminal');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'view_instructions');
		$this->text('Instructions');
		$this->endElement();
		$this->startElement('li');
		$this->writeAttribute('id', 'view_fullscreen');
		$this->text('Fullscreen Mode');
		$this->endElement();
		$this->endElement();
	}
	protected function fileInput() {
		$this->startElement('div');
		$this->writeAttribute('id', 'input_select');
		$this->writeAttribute('class', 'window');
		$this->startElement('form');
		$this->startElement('input');
		$this->writeAttribute('type', 'file');
		$this->writeAttribute('id', 'local_file_open');
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'button_rack');
		$this->startElement('button');
		$this->writeAttribute('id', 'input_select_close_button');
		$this->writeAttribute('class', 'center');
		$this->text('Close File Input');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function displayInstructions() {
		$this->startElement('div');
		$this->writeAttribute('id', 'instructions');
		$this->writeAttribute('class', 'window');
		$this->startElement('div');
		$this->writeAttribute('id', 'keycodes');
		$this->startElement('h1');
		$this->text('Keyboard Controls:');
		$this->endElement();
		$this->startElement('ul');
		$this->startElement('li');
		$this->text('X is A.');
		$this->endElement();
		$this->startElement('li');
		$this->text('Z is B.');
		$this->endElement();
		$this->startElement('li');
		$this->text('Shift is Select.');
		$this->endElement();
		$this->startElement('li');
		$this->text('Enter is Start.');
		$this->endElement();
		$this->startElement('li');
		$this->text('The d-pad is the control pad.');
		$this->endElement();
		$this->startElement('li');
		$this->text('The escape key (esc) allows you to get in and out of fullscreen mode.');
		$this->endElement();
		$this->endElement();
		$this->endElement();
		$this->startElement('div');
		$this->writeAttribute('class', 'button_rack');
		$this->startElement('button');
		$this->writeAttribute('id', 'instructions_close_button');
		$this->writeAttribute('class', 'center');
		$this->text('Close Instructions');
		$this->endElement();
		$this->endElement();
		$this->endElement();
	}
	protected function fullscreenGenerate() {
		$this->startElement('div');
		$this->writeAttribute('id', 'fullscreenContainer');
		$this->startElement('canvas');
		$this->writeAttribute('id', 'fullscreen');
		$this->writeAttribute('class', 'maximum');
		$this->endElement();
		$this->endElement();
	}
}
$site = new GameBoy();
?>