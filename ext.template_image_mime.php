<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Template Type Image MIME
 * ----------------------------------------------------------------------------------------------
 * Adds image types to the EE template Preferences utilizing the EE template_types hook
 * ----------------------------------------------------------------------------------------------
 * @package	EE2 
 * @subpackage	ThirdParty
 * @author	Tyson Oshiro (for Kamehameha Schools)
 * @link	http://www.ksbe.edu 
 * 
 */
 
class Template_image_mime_ext
{
    var $name      		= 'Template Image Mime Type';
    var $version        = '1.0';
    var $description    = 'Adds an image mime types to the template';
    var $settings_exist = 'n';
    var $docs_url       = ''; 
	var $settings 		= '';
	var $template_types = '';
	var $sn_prefix 		= 'ttim'; // shortname prefix to avoid template type collisions
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param string $settings (default: '')
	 * @return void
	 */
	public function __construct($settings='') 
	{	
		$this->settings = $this->_settings(); 
	}


	/**
	 * template_types function.
	 * 
	 * Called by the template_types hook
	 * @access public
	 * @return void
	 */
	public function template_types() 
	{
		if( !ee()->extensions->active_hook('template_types') ) 
			return;
			
		return 	$this->add_custom_template_type();

	} // end template_types


	/**
	 * add_custom_template_type function.
	 * 
	 * @access public
	 * @return void
	 */
	public function add_custom_template_type() 
	{
		$custom_templates = ee()->extensions->last_call;
		
		foreach( $this->settings AS $ext=>$template_dataset ) {
		
			$custom_templates["$ext"] = array('template_name' 			=> $template_dataset['template_name'],
										      'template_file_extension' => $template_dataset['template_file_ext'],
										      'template_headers'        => $template_dataset['template_headers']);
		}
		return $custom_templates;
		
	} // end add_custom_template_type


	/**
	 * settings function.
	 * 
	 * @access private
	 * @param string $fileType (default: '')
	 * @return void
	 */
	private function _settings() 
	{
			$settings[$this->sn_prefix."_jpg"] = array('template_name'	=> 'Image: JPG',
							  		 				   'template_file_ext' => '.jpg',
							  		 				   'template_headers' => array('Content-Type: image/jpg')
							  		 				   );
			$settings[$this->sn_prefix."_gif"] = array('template_name'	=> 'Image: GIF',
							  		 				   'template_file_ext' => '.gif',
							  		 				   'template_headers' => array('Content-Type: image/gif')
							  		 				   );
			$settings[$this->sn_prefix."_png"] = array('template_name'	=> 'Image: PNG',
							  		 				   'template_file_ext' => '.png',
							  		 				   'template_headers' => array('Content-Type: image/png')
							  		 				   );
			$settings[$this->sn_prefix."_swf"] = array('template_name'	=> 'Image: SWF',
							  		 				   'template_file_ext' => '.swf',
							  		 				   'template_headers' => array('Content-Type: application/x-shockwave-flash')
							  		 				   );
		return($settings);	
	} // end _settings
	


	/**
	 * Activate Extension
	 * @return void
	 */
	public function activate_extension()
	{

	    $data = array(
	        'class'     => __CLASS__,
	        'method'    => 'template_types',
	        'hook'      => 'template_types',
	        'settings'  => serialize($this->settings),
	        'priority'  => 10,
	        'version'   => $this->version,
	        'enabled'   => 'y'
	    );	
	    ee()->db->insert('extensions', $data);
	    
	} // end activate_extension


	/**
	 * Disable Extension
	 * @return void
	 */
	public function disable_extension()
	{
		$defaultTemplateType = 'webpage'; // replace custom templates types with the default value 'webpage'
		
	    ee()->db->where('class', __CLASS__);
	    ee()->db->delete('extensions');
	    
		// Clean-up template table 
		foreach( $this->settings AS $ext=>$notused ) {	    
		    ee()->db->where('template_type', $ext);
		    ee()->db->update('templates', array('template_type'=>$defaultTemplateType));
	    }
	    
	} // end disable_extension
	

	/**
	 * Update Extension
	 * @return  mixed   void on update / false if none
	 */
	public function update_extension($current = '')
	{
	
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    if ($current < '1.0')
	    {
	        // Update to version 1.0 - No updates yet
	    }
	
	    ee()->db->where('class', __CLASS__);
	    ee()->db->update(
	                'extensions',
	                array('version' => $this->version)
	    );
	    
	} // end update_extension

} // end class