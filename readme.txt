=== TranslateMyBlog ===
Contributors: translatemyblog
Donate link: http://translatemyblog.com/
Tags: translate, translation, language, international, revenue
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.0

Get professional human translation for your blog... for free!  More languages = more readers = more revenue.

== Description ==

TranslateMyBlog is a free service (now in beta) that offers professional human translation for your blog.  When you sign up, we translate your most popular posts into the world's most widely read global languages.

The translated posts live on your blog (in a separate section) and look just like any other post.  The only difference is that we place a single Google Adsense block at the bottom of each translation (that's how we generate revenue).  Any ads you run on your blog will also appear as normal on the translated post pages.

[Homepage](http://translatemyblog.com/)

[Live Demo](http://www.translatemyblog.com/blog/2009/03/translatemyblog-demo/)

Why use TranslateMyBlog:

*   Increase your Google rank by attracting links from blogs in other languages.
*   Increase your page views by reaching new readers who don't speak your language.
*   More page views + more page rank = more ad revenue.

You can deactivate the service at any time, and keep any existing translations (we just ask that you not remove our adsense block).

== Installation ==

NOTE: The service is now in private beta (though anyone is free to experiment with our plugin).  If you'd like to participate, please [contact us](mailto:editor@translatemyblog.com) and include a link to your blog.  

Installation instructions for beta users:

1. **Download** the plugin here.
1. **Install Plugin** Go to Plugins > Add New.  Click Browse, and select "translate_my_blog.zip" for upload.  Click "Install Now"..
1. **Activate plugin** Once the upload is complete, click "Activate Plugin"
1. **Agree to terms / Grant access** Once the plugin is activated, go to Settings > Translate My Blog in the left sidebar of your Dashboard.  Click Agree / Grant access.  This will let our professional translators post translations to your site.  It will also install our Google Analytics script so that we can prioritize your most popular posts for fastest translation (note: if you use Analytics already, disable this option after granting access).
1. **Install widgets** Go to Appearance > Widgets in the left sidebar of your Dashboard.  Drag the two "Translations" widgets to your sidebar and click "Save Changes".  
1. **Add code** to your templates (recommended).  You can display Translate My Blog links below the post title by adding the code below to your templates.
1. **That's it on your side!**  On our side, we'll try to figure out what posts will be popular with people who speak other languages, and add a translation here and there. 

**Code to add to your templates** (It's painless, and gives your users a better experience):

To display translation links under the title of the post (where they'll be more visible to users), go to Appearence > Editor, edit your Main Index Template (index.php) and insert

`<div><?php if(function_exists('translatemyblog_display_translations')) {translatemyblog_display_translations($id);} ?></div>`

...before the line <div class="entry"> or after the post title.

To display links back to the original post on the translation page (along with links to translations in other languages) edit your Single Post (single.php) template and insert

`<div><?php if(function_exists('translatemyblog_display_translations')) {translatemyblog_display_translations($id);} ?></div>
<div><?php if(function_exists('translatemyblog_display_parent_link')) {translatemyblog_display_parent_link($id);} ?></div>`

...before the line <div class="entry"> or after the post title.

**Uninstalling TranslateMyBlog**

You can revoke access and disable our Google Analytics by clicking "Revoke Access" in settings. This will preserve all existing translations.  To deactivate and remove everything, click "Remove User" on the settings page and then delete the user, deleting all posts. 

**Layout and Styling**  

You can style what is displayed by the plugin using CSS, in your theme file.  The block of translation links that appears in your sidebar is a wordpress widget, and you can change its position in Appearance -> Widgets in the Dashboard. 

== Frequently Asked Questions ==

= I installed your plugin from the Wordpress directory, but no translations appear. =

Right now the free translation service is in private beta. Contact us (editor at translatemyblog) if you’d like to participate.

= Is this machine translation or human translation? =

Your posts will be translated by real, live professional translators. We plan to offer a machine translation option in the near future.

= How can you provide professional translation for free? =

We believe that the posts you write on your blog will be interesting to many people who don’t speak your language. When we do a free translation, we attach a single Google Adsense block to the translation itself. As people find your translated post, we’ll recover the cost of translation. But most importantly, you get more readers (and more revenue) too!

= Can I pick which posts are translated? =

Not right now, but you can suggest certain posts to be translated.

= Why do you use Google Analytics? =

We use Google Analytics (along with other techniques) to figure out what articles–once translated–will bring the most new readers to your blog. We will only use Analytics to optimize our service to you, and will not share your data with anyone.

= My Google Analytics stopped working when I installed your plugin. = 

Sorry about that. Google Analytics breaks when two bits of tracking code are installed on the same site (we try to make this clear in the install instructions). Just disable Analytics on the TranslateMyBlog settings page. We’ll use other information to prioritize posts for translation.

= What if I stop using your service? =

No problem. You can revoke access (ending our ability to submit translations) at any time. You can also uninstall the plugin or its widgets (removing language icons on translated posts) and either keep or delete all translations. If you choose to keep our translations on your site, we ask that you not remove our Adsense block.

== Screenshots ==

1. Translation links in the title of a blog post
2. Translation links in a sidebar widget