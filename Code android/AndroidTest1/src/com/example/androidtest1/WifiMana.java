package com.example.androidtest1;

import java.util.ArrayList;
import java.util.List;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiManager;
import android.os.Looper;

public class WifiMana implements Runnable{
	
	
	Thread ourThread = null;
	boolean isRunning = false;	
	private String message; 
	WifiTabs wt;
	
	public void pause() {
		isRunning = false;
		while (true) {
			try {
				ourThread.join();
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			break;
		}
		ourThread = null;
	}
	
	public void resume() {
		isRunning = true;
		ourThread = new Thread(this);
		Looper.prepare();
		ourThread.start();
		System.out.println("hoi1");
		
	}
	
	public void setText(String message){
		
		this.message=message;
		
	
	}
	
	public String getText(){
		
		return this.message;
		
	}
	
	@Override
	public void run() {
		Looper.prepare();
		while (isRunning) {
			wt = new WifiTabs();
			//setText("accespoint: " + wt.getAB() + " distance: "+ wt.getAD());
			//System.out.println(wt.getAB());
		
		}
		
		
	}



}
