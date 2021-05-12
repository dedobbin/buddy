package com.example.wtfdongs;

public class Person {
	private String name, room;
	private int tempInRoom;
	private boolean online;
	public Person(String name, String room, int tempInRoom, boolean online){
		this.name = name;
		this.room= room;
		this.tempInRoom = tempInRoom;
		this.online = false;
	}
	
	public String getName(){
		return name;
	}
	public String getRoom(){
		return room;
	}
	public int getTempInRoom(){
		return tempInRoom;
	}
	public boolean isOnline(){
		return online;
	}
}
