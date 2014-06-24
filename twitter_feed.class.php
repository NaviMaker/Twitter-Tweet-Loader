<?php
require_once("twitteroauth/twitteroauth.php");

class twitterLoadFeed
{
	//The user you want to load tweets
	public $username;
	//Iclude Retweets
	public $retweets = false;
	//Ignore Replies
	public $replies = false;
	
	//Oauth keys and access tokens you can get them registering your app here: https://apps.twitter.com/app/new
	public $consumerkey;
	public $consumersecret;
	public $accesstoken;
	public $accesstokensecret;
	
	//Maximum number of twitts to load
	public $tweetsLimit = 5;
	//Use dates like "1 minute ago"
	public $useAdvancedDates = true;
	//Output format: 1 XML, 2 JSON, 3 PLAIN TEXT
	public $outputFormat = 1;
	//Enable cache for feeds, this procedure is highly recommended not to exceed the 150 request/hour limit
	public $useCache = false;
	//Cache file, not existent it will be generated
	public $cacheFile = "";
	//Time in seconds before refreshing the cache file, usually 1 minute is a good value
	public $cacheStoreTime = 60;
	
	//This variable will be filled with your tweets
	public $connection;
	public $feed;
	
	
	//Check if there is a valid cache file
	function cacheCheck()
	{
		if(file_exists($this->cacheFile))
		{
			if(filemtime($cache_file) > time()-$this->cacheStoreTime)
			{
				return true;
			}
		}
		return false;
	}
	
	//Create cached version of the output or update an existing one
	function cacheGenerate()
	{
		if(is_writable($this->cacheFile))
		{
			if(file_put_contents($this->cacheFile, $this->feed, FILE_APPEND))
			{
				return true;
			}
			return false;
		}
		return false;
	}
	
	//Open Oauth connection
	function openConnection()
	{
		return new TwitterOAuth($this->consumerkey, $this->consumersecret, $this->accesstoken, $this->accesstokensecret);
	}
	
	//Format tweets
	function formatTweets($tmp)
	{
		$this->feed=array();
		$i=0;
		foreach($tmp as &$t)
		{
			$this->feed[$i]=array(
				"text" => $this->addLinks($t->text),
				"retweet_count" => $t->retweet_count,
				"favorite_count" => $t->favorite_count,
				"created_at" => $t->created_at,
			);
			$i++;
		}

	}
	
	//Add Links to hashtags and urls
	function addLinks($text)
	{
		return $text;

	}
	
	//Format Date
	function formatDate($date)
	{
		return $date;

	}
	
	//Open a connection and load twitter feeds
	function loadFeed(){
		if( ($this->useCache) && ($this->cacheCheck()) )
		{
			$this->feed = file_get_contents($this->cacheFile);
		}
		else
		{
			$this->connection = $this->openConnection();
			
			if($this->connection)
			{
				$tmp = $this->connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$this->username."&count=".$this->tweetsLimit."&include_rts=".$this->retweets."&exclude_replies=".$this->replies);
				
				if(count($tmp) > 0)
				{
					$this->formatTweets($tmp);
					print_r($tmp);
					return $this->feed;			
				}
				return false;
			}
			return false;
		}
	}
}

?>