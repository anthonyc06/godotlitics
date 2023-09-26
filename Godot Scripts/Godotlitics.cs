using Godot;
using System;
using System.Text;
using System.Net;
using System.Net.Http;

public partial class Godotlitics : Node 
{
	// Replace with your Godotlitics API key in configuration string
	string API_KEY; 
	
	bool verbose = true;
	
	public enum EventType
	{
		LaunchGame = 0,
		ViewScreen = 1,
		Progression = 2,
		AdShowed = 3,
		Error = 4
	}
	
	string gameName;
	string studioName;
	string gameVersion;
	string userId = "";
	string baseUrl;
	
	bool initialized = false;
	bool configDone = true;
	
	// Initialise Godotlitics script
	public void Init()
	{
		if (!initialized)
		{
			Guid guid = Guid.NewGuid();
			userId = userId == "" ? guid.ToString() : userId;
			if (verbose) GD.Print("UserId : " + userId);
			
			ParseConfiguration();
		}
		else
		{
			if (verbose) GD.Print("Godotlitics already initialized!");
		}
	}
	
	// Parse configuration string in project settings
	public void ParseConfiguration()
	{
		// Game name
		gameName = ProjectSettings.GetSetting("application/config/name").ToString();
		if (verbose) GD.Print("Game name : " + gameName);
			
		string gameDesc = ProjectSettings.GetSetting("application/config/description").ToString();
		// Parse configuration string
		string[] gameInfos = gameDesc.Split(';');

		// Studio name
		if(gameInfos.Length > 0 && gameInfos[0] != null && gameInfos[0] != "")
		{
			studioName = gameInfos[0];
			if (verbose) GD.Print("Studio name : " + studioName);
		} 
		else
		{
			studioName = "";
			GD.Print("Warning, missing company name!");	
		}
		
		// Game version
		if(gameInfos.Length > 1 && gameInfos[1] != null && gameInfos[1] != "")
		{
			gameVersion = gameInfos[1];
			if (verbose) GD.Print("Game version : " + gameVersion);
		} 
		else
		{
			gameVersion = "";
			GD.Print("Warning, missing game version in configuration string!");	
		}
		
		// API base URL
		if(gameInfos.Length > 2 && gameInfos[2] != null && gameInfos[2] != "")
		{
			baseUrl = gameInfos[2];
		} 
		else
		{
			configDone = false;
			GD.Print("*** Error, missing API base URL in configuration string!");	
		}
		
		// API key
		if(gameInfos.Length > 3 && gameInfos[3] != null && gameInfos[3] != "")
		{
			API_KEY = gameInfos[3];
		} 
		else
		{
			configDone = false;
			GD.Print("*** Error, missing API KEY in configuration string!");	
		}
		
		if (configDone)
		{
			initialized = true;
			if (verbose) GD.Print("Godotlitics ready!");
		}
		else
		{
			GD.Print("*** Error while initializing Godotlitics. Please check your configuration string in project settings (see Godolitics docs for more informations).");
		}
	}
	
	// Return event name by event ID
	string GetEventName(int eventId)
	{
		string[] eventNames = {"launch_game", "view_screen", "progression", "ad_showed", "error"};
		return eventNames[eventId];
	}
	
	// Track event with parameter
	public async void TrackEvent(EventType eventType, string eventParam = "")
	{
		if (configDone)
		{
			string eventName = GetEventName((int)eventType);
			var url = baseUrl+"addEvent.php?studioName="+studioName+"&gameName="+gameName+"&gameVersion="+gameVersion+"&eventType="+eventName+"&eventParam="+eventParam+"&userId="+userId+"&key="+API_KEY;
			using var client = new System.Net.Http.HttpClient();
			var result = await client.GetStringAsync(url);
			
			if (verbose)
				GD.Print("Tracked event: '" + eventName + "' with param '" + eventParam + "', row " + result + " inserted.");
		}
		else {
			GD.Print("*** Error, please initialize Godotlitics before tracking events!");
		}
	}
	
	// Game launched event
	public void TrackLaunchGame()
	{
		TrackEvent(EventType.LaunchGame);
	}
	
	// Current scene viewed event
	public void TrackViewCurrentScene()
	{
		string sceneName = GetTree().CurrentScene.Name;
		TrackEvent(EventType.ViewScreen, sceneName);
	}
	
	// Progression event with custom progression name
	public void TrackProgression(string progress)
	{
		TrackEvent(EventType.Progression, progress);
	}
	
	// Ad showed event with custom ad nickname
	public void TrackAdShowed(string adName)
	{
		TrackEvent(EventType.AdShowed, adName);
	}
	
	// Error event with custom error message
	public void TrackError(string err)
	{
		TrackEvent(EventType.Error, err);
	}
	
}
