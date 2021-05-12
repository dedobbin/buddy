package com.example.buddy;

public class Storage {
	private String localTemp;
	private String user;
	private String state;
	private String prefTemp;
	private String userClassroom;

	
	public void setLocalTemp(String localTemp) {
		this.localTemp=localTemp;
	}

	public String getLocalTemp(){
	return this.localTemp;
	}
	public void setUser(String user) {
		this.user=user;
	}

	public String getUser(){
	return this.user;
}
	public void setState(String state) {
		this.state=state;
	}

	public String getState(){
	return this.state;
}
	public void setPrefTemp(String prefTemp) {
		this.prefTemp=prefTemp;
	}

	public String getprefTemp(){
	return this.prefTemp;
}
	public void setUserClassroom(String userClassroom) {
		this.userClassroom=userClassroom;
	}

	public String getUserClassroom(){
	return this.userClassroom;
	}			
}		