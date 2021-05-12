package com.example.buddy;

public class Person {
	private String name, room;
	private double tempInRoom;
	private boolean online;
	public Person(String name, String room, double tempInRoom, boolean online){
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
	public double getTempInRoom(){
		return tempInRoom;
	}
	public boolean isOnline(){
		return online;
	}
}
