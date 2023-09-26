using Godot;
using System;

public partial class Example : Node3D
{
	
	public override void _Ready()
	{
		// Reference to Godotlitics
		var gdlitics = GetNode<Godotlitics>("/root/Godotlitics");
		
		// Godotlitics initialization
		gdlitics.Init();
		
		// Track event by type and parameter
		gdlitics.TrackEvent(Godotlitics.EventType.LaunchGame, "Example param");
	
		// Track game launched event
		gdlitics.TrackLaunchGame();
		
		// Track gcurrent scene viewed event
		gdlitics.TrackViewCurrentScene();
		
		// Track progression event
		gdlitics.TrackProgression("New skin unlocked");
		
		// Track ad showed event
		gdlitics.TrackAdShowed("Bottom banner");
		
		// Track error event
		gdlitics.TrackError("Oops, menu broken!");
	
	}
	
	
}
