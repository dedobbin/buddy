package com.example.wtfdongs;

import android.app.Activity;

public class Storage {
	private static int  prefTemp, localTemp;
	private static String name;
	private static String[] friends;
	private static Person ownInfo;
	private static Person[]liveFriendList;
	
	public static void setLocalTemp(int in){
		localTemp = in;
	}
	
	public static int getLocalTemp(){
		return localTemp;
	}
	public static void setLiveFriendsList(Person []in){
		liveFriendList = in;
	}
	public static Person[] getLiveFriendsList(){
		return liveFriendList;
	}
	
	public static void setPrefTemp(int in){
		prefTemp = in;
	}
	public static int getPrefTemp(){
		return prefTemp;
	}
	public static void setName(String in){
		name = in;
	}
	public static String getName(){
		return name;
	}
	public static void setFriendIDs(String [] in){
		friends = in;
	}
	public static String[] getFriendIDs(){
		return friends;
	}
	public static void setOwnInfo(Person self){
		ownInfo = self;
	}
	public static Person getOwnInfo(){
		return ownInfo;
	}



}
