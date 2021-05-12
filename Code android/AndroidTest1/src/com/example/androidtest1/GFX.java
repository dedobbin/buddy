package com.example.androidtest1;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.os.PowerManager;
import android.os.PowerManager.WakeLock;
import android.view.WindowManager;

public class GFX extends Activity{

	MyBringBack ourView;
	WakeLock wL;
	PowerManager pM;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		
		//wake-lock
		//pM = (PowerManager)getSystemService(Context.POWER_SERVICE);
		//wL = pM.newWakeLock(PowerManager.FULL_WAKE_LOCK, "whatever");
		this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON, WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
		super.onCreate(savedInstanceState);
//		wL.acquire();
		ourView = new MyBringBack(this);
		setContentView(ourView);
		
	}

//	@Override
//	protected void onPause() {
//		// TODO Auto-generated method stub
//		super.onPause();
//		wL.release();
//	}
//
//	
//	
//	
}
