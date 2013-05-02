<?php if (!defined('APPLICATION')) exit();
// Define the plugin:

$PluginInfo['Galleries'] = array(
   'Name' => 'Galleries',
   'Description' => 'Adds an Image Gallery to you website for you and users to add new pictures to the gallery.<br/>You can create seperate albums and upload forms for everyone to have their own personal album, or create a community album for everyone.<br/>',
   'Version' => '1.1',
   'Author' => 'VrijVlinder',
   'AuthorEmail' => 'contact@vrijvlinder.com',
   'AuthorUrl' => 'http://www.vrijvlinder.com',
'RegisterPermissions' => array('Plugins.Attachments.Upload.Allow')


  
);



class Galleries extends Gdn_Plugin {

  public function Base_Render_Before($Sender) {
		
$Session = Gdn::Session();

       if ($Sender->Menu){
$Sender->Menu->AddLink('Gallery', T('Gallery'),'/gallery',array('Garden.SignIn.Allow'),array(), array('target' => '_blank')); 
	}
	
 }



	public function PluginController_Gallery_Create($Sender) {
			
                        $Sender->ClearCssFiles();
			$Sender->AddCssFile('style.css');
                        $Sender->AddCssFile('gallery.css');
			$Sender->MasterView = 'default';
			$Sender->Render($this->GetView('gallery.php'));
                       
	}


	
	public function Setup() { 
		Gdn::Router()->SetRoute('gallery','plugin/gallery','Internal');
		if(!is_dir('uploads/picgal/')) $go = mkdir('uploads/picgal/', 0775);
	}
	
	 public function OnDisable() {
		SaveToConfig('EnabledPlugins.Galleries', FALSE);
   }
	
}
