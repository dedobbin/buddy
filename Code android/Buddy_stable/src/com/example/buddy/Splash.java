package com.example.buddy;


import android.app.ActionBar;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;

public class Splash extends Activity{

	@Override
	protected void onCreate(Bundle bundle) {
		// TODO Auto-generated method stub
		
		super.onCreate(bundle);
		setContentView(R.layout.splash);
		ActionBar actionBar = getActionBar();
		actionBar.hide();
		Thread timer = new Thread(){
			public void run(){
				try{
					sleep(3000);
				}catch(InterruptedException e){
					e.printStackTrace();
				}finally{
					Intent intent = new Intent(Splash.this, MainActivity.class);
			        startActivity(intent);
				}
				
			}
		};
		timer.start();
	}

	@Override
	protected void onPause() {
		// TODO Auto-generated method stub
		super.onPause();
		finish();
	}
	
	

}
