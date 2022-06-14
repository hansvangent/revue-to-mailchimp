# Revue to Mailchimp
Add new Revue subscribers (native Twitter newsletter subscription) to your Mailchimp Newsletter account

## Convert your Twitter followers to newsletter subscribers

Revue to Mailchimp syncs your Twitter newsletter subscribers to your existing Mailchimp account.

Your followers can easily subscribe on Twitter, and you can keep using the tools you love.

Wait what?

**Twitters new Revue Badge**
Twitter recently acquired a newsletter organization called Revue. Twitter has now made it possible for you to add a badge to your Twitter profile, where people can easily subscribe to your Revue newsletter. This is awesome and might be a gamechanger for a lot of writers! But what if you want this badge, without having to switch to Revue?

Use the Revue badge to grow your newsletter, without switching to Revue.

This script makes it possible for you to get new subscribers to your existing newsletter on Mailchimp, without having to switch to Revue.

It will collect your new subscribers in Revue and add them to your existing newsletter Mailchimp Newsletter list.

When set up using cron, it will keep an eye out for new subscribers on your Revue list and will automatically add them to your existing newsletter list.

Set up once, grow your newsletter forever!

It is super easy to set up! It will only take 5 minutes and will enable you to grow your newsletter via your Twitter profile forever!

Oh and it also check anyone that unsubscribes from your Mailchimp list and unsubscribes them also from your Revue account to make sure both are always in sync.


## 4 easy steps to get started
### Step 1 Create a Revue account
Create a Revue account, [enable the badge](http://help.getrevue.co/en/articles/5356115-how-to-show-your-newsletter-on-your-twitter-profile) on your Twitter profile and [get your API key](https://www.getrevue.co/app/integrations) to set up this script.

Add your API key in the revue-to-Mailchimp.php by replacing replace_me_with_your_revue_api_key on line 20 with your own key:

```php
$revue_api_key = 'replace_me_with_your_revue_api_key';
```

### Step 2 Get you Mailchimp API key and Email Category ID's
Next up you need to the the [API key from your Mailchimp](https://us1.admin.mailchimp.com/account/api/) account

Add your API key in the revue-to-mailchimp.php by replacing *replace_me_with_your_mailchimp_api_key* on line 22 with your own key:

```php
$mailchimp_api_key = 'replace_me_with_your_mailchimp_api_key';
```

Next up you will need the Audience ID or List ID so we know to which list the new contact in MailChimp need to be subscribed. More information about [finding your Audience ID here](https://mailchimp.com/en-gb/help/find-audience-id). In short, MailChimp Audience ID can be found by clicking on Audience > All contacts > Settings > Audience name and defaults > In the Audience ID section, you’ll see a string of letters and numbers. This is your audience List ID.

Add your List ID in the revue-to-mailchimp.php by replacing *replace_me_with_your_mailchimp_list_id* on line 23 with your own key:

```php
$mailchimp_list_id = 'replace_me_with_your_mailchimp_list_id';
```

Finally we need to know on which of the Mailchimp servers your account is hosted. After you've logged into your account you can see this in your address bar, e.g. us1. Add this server prefix on line 24 by replacing *replace_me_with_your_mailchimp_server_prefix* with that value.

```php
$mailchimp_server_prefix = 'replace_me_with_your_mailchimp_server_prefix';
```

That's all the configuration needed.

### Step 3 Set up a cron job to run the script

Since this will be a script that is designed to run from the command line (although you can let it run from a webserver and invoke a regular check like that too), you will need to [set up a cron job](https://askubuntu.com/questions/2368/how-do-i-set-up-a-cron-job).

For example to run the check every 15 minutes:

``` bash
*/15 * * * * /usr/bin/php /path/to/revue-to-Mailchimp.php
```

Not sure if /usr/bin/php is the correct path to your PHP binary?

Start with finding out your PHP binary by typing in command line:

``` bash
whereis php
```

The output should be something like:

``` bash
php: /usr/bin/php /etc/php.ini /etc/php.d /usr/lib64/php /usr/include/php /usr/share/php /usr/share/man/man1/php.1.gz
```

Specify correctly the full path in your cron-job.

### Step 4 Launch & Grow
That's all. From now on, you can use your Twitter profile to boost the growth of your newsletter. Let's grow!

## Want to grow not just your email list but also your Twitter followers?
You can do this by automatically sharing your best Evergreen Content with your Twitter followers.

This way you can build a following and attract more subscribers to your newsletter.

Simply install the [Evergreen Content Poster](https://www.evergreencontentposter.io/) on your WordPress website. It will help you to easily double your traffic from social media by keeping your content alive and in front of your target audience.

The **Evergreen Content Poster is a unique social media scheduler that does the sharing for you**, by automatically pulling posts from your content library and sharing it to your social media channels.

So you can keep your social media alive every day, on repeat.

## Get Help

- Reach out on [Twitter](https://twitter.com/jcvangent)
- Open an [issue on GitHub](https://github.com/hansvangent/revue-to-mailchimp/issues/new)

## Contribute

#### Issues

In the case of a bug report, bugfix or a suggestions, please feel very free to open an issue.

#### Pull request

Pull requests are always welcome, and I'll do my best to do reviews as fast as I can.
