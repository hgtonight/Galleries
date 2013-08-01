*****In this version I made the linked images be resized larger and fixed a path****


This plugin is based on Picture Gallery 0.2.1 by Yohn,john@skem9.com,
http://www.skem9.com later edited by Trey but did not work,So I made a new one based on parts of the old one. This does not use a manager and the only settings are the permissions to upload. 

This version completely modified by VrijVlinder, with collaboration from businessdad and encouragement from peregrine UnderDog lifeisfoo and the rest of the Vanilla community who have taught me very much. 

contact@vrijvlinder.com

Add this plugin to your plugin folder and enable from the dashboard. You first need to create a new Album and then the form appears in the Album you just created so you can upload pictures to it. Also can edit the names and delete them.

Only Logged in users can upload and make new folders. The link to the Gallery only appears to logged in users.

To erase the Albums, they are located in the uploads folder under uploads/picgal

To upload more than one at a time you can ftp to the specific folder and copy the names of the files into the uploads/picgal/yourgalleryalbumname/notes.dat.php  so they can display.

Depending on how you have your forum set up, you may need to change the path to some things, if the name of your directory where the forum is stored is not called /forum/
you need to change that in the gallery.php where the path indicates /forum/ , or forum/gallery. or forum. 

Before you mess it up best ask for guidance if you think you can't do it or don't know how.

You can use this with the VanillaFancyBox plugin, if you do then you can choose to show less images and you can view all the images from the fancy box. You may need to add the plugincontroller to the array of places the fancy box will be used on.


If your forum is not called forum, please change all the parts that say forum to the name of the forum. If your forum is in the root please replace all the parts that say forum with a /