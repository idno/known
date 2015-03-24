>The following installation guide will walk you through the steps to set up a Known site on Arvixe using only the CPanel.

Arvixe is a web host provider with reliable hosting for Known.  Visit the [Arvixe Known hosting page](http://www.arvixe.com/9014-443-3-367.html) to sign up for an Arvixe hosting account that meets the Known platform requirements.

![Arvixe account](https://withknown.com/img/docs/arvixe/01-arvixe.png)

During the account creation process, you'll be able to buy a domain name for your site or add in details for a domain that you already own.

![CPanel](https://withknown.com/img/docs/arvixe/02-cpanel-login.png)

Once you have an account, navigate to your site's CPanel.  You should have received an email from Arvixe with account details, including the CPanel address.  Your site's CPanel might be found at **http://cpanel.yourawesomesite.com**.

![CPanel](https://withknown.com/img/docs/arvixe/03-cpanel.png)

##Setting PHP 5.4##
Once you're in the CPanel, navigate to ntPHPSelector.

![ntPHPSelector](https://withknown.com/img/docs/arvixe/04-ntphpselector.png)

From the folder list there, choose public_html.

![Choose the public folder](https://withknown.com/img/docs/arvixe/05-publichtml.png)

Once you've clicked it, select PHP 5.4 or PHP 5.5 from the "Preferred directory" dropdown and choose "Submit."

![Select your PHP version](https://withknown.com/img/docs/arvixe/06-phpselect.png)

Next, go back to the CPanel main screen.

##Uploading the files##
In order to install Known, you'll need the latest package of the software.  You can download the latest release of Known from the [homepage of our site(https://withknown.com) or from our [developers page](https://withknown.com/developers/).

In the CPanel of your site, navigate to "File Manager" under "Files."

![File Manager](https://withknown.com/img/docs/arvixe/07-filemanager.png)

Choose "Document Root" for your domain.

![Choose the document root](https://withknown.com/img/docs/arvixe/08-documentroot.png)

You should now see a screen that says, "This directory is empty."

![Empty directory](https://withknown.com/img/docs/arvixe/09-empty.png)

Click the "upload" icon.

From the upload screen, browse and find the ZIP file for the Known package that you just downloaded from our site.

Select the ZIP file, and then let it upload. There should be a status indicator in the lower right corner of your screen showing the upload progress.

![Progress bar](https://withknown.com/img/docs/arvixe/10-upload-progress.png)

Once the upload is complete, navigate back using the link in the middle of the screen.

You should now see your Known package in the public-html folder.

Click on the package. Then select the "Extract" option.

![Extract the package](https://withknown.com/img/docs/arvixe/11-package-extract.png)

Extract the package to the public_html directory.

![Extract your package to public folder](https://withknown.com/img/docs/arvixe/12-extract-to.png)

When the package is done extracting, there should be a window with some extraction results. Your public_html folder should also be full of Known files now.

![Extracted files](https://withknown.com/img/docs/arvixe/13-extraction-results.png)

##Create a database for your site##
Navigate back to the CPanel for your site.

Choose the MySQL Database Wizard icon.

![Database Wizard](https://withknown.com/img/docs/arvixe/14-mysqlwizard.png)

Now you're going to be creating a database for your website and a user for your database. You may want to write down the details that you choose here, because you're going to need to refer back to them a little later.

In the database wizard, give your database a name. It should have a database prefix listed. Mine is erichey_. I named my database "known" for clarity, but you can choose anything that you want.

![Enter your database name](https://withknown.com/img/docs/arvixe/15-createdb.png)

Next, you need to create a user by setting a username and a password in the wizard.

![Enter your database username](https://withknown.com/img/docs/arvixe/16-dbuser.png)

Once the database and the database user have been set up, you need to add them to the database. Select "All Privileges" for now to give your new user all of the database privileges.

![Set your privileges](https://withknown.com/img/docs/arvixe/17-privileges.png)

Now you should be good to go!

##Set up your site##
Navigate to the URL of your website.

Your URL should forward to **yourdomain.com/warmup/** and you should see a screen that says "Let's get started."

![Welcome to your new site](https://withknown.com/img/docs/arvixe/18-welcome.png)

After you click the button, you should be on a requirements page. This page is a check to make sure your Arvixe space meets the requirements for Known. Known needs PHP 5.4 to work, so if you didn't set it in the first step, you won't be able to complete your install.

![Check your requirements](https://withknown.com/img/docs/arvixe/19-requirements.png)

On the requirements page, green items are good and red items indicate that something isn't compatible. If you see a message that says it can't detect Apache mod_rewrite, you can continue anyway.  Click the "Hooray!" button to move on.

![Hooray](https://withknown.com/img/docs/arvixe/20-hooray.png)

Now it's time to enter the database details that you recently set up.

First, name your new website. This will show up in the navigation of your site.

![Give your site a name](https://withknown.com/img/docs/arvixe/21-name.png)

Next, enter in your database name (with the prefix), the database username (with the prefix), and the password. You can leave the field that says "localhost" as is.

![Enter your database details](https://withknown.com/img/docs/arvixe/22-sql-settings.png)

Under the section for "Your upload directory" leave the field as is.

![Your upload directory](https://withknown.com/img/docs/arvixe/23-directory.png)

If you didn't enter in the correct database name, username, or password, you'll get an error message saying that it can't connect to the database. If this is the case, you'll need to go back and enter in the correct details.

![Error message](https://withknown.com/img/docs/arvixe/24-error.png)

##Create your user account##
Now you should see a screen asking you to create a new user account. This will be your site's admin account. Fill out your details and make sure you include a relevant email address. Site notifications will be sent there.

![Create a user account](https://withknown.com/img/docs/arvixe/25-account.png)

Once you've registered a new user account, you have an opportunity to fill out your profile.  You can upload an image, add in a short bio, and include some of your websites.  If you don't want to fill this out now, you can always come back and add these details in your site later.

![Create your profile](https://withknown.com/img/docs/arvixe/26-profile.png)

Once you've saved your user profile, you should be on the compose view of your new site! Now you can start publishing right away.  If you want to make more customizations, you can visit your site configuration to change some of the settings, change your theme, or invite other authors to join your site.

![Your new site](https://withknown.com/img/docs/arvixe/27-home.png)

You may also want to create the social network plugins for your site now.