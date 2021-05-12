package com.example.buddy;


import com.example.buddy.WifiIni;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.support.v4.widget.DrawerLayout;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;

public class MainActivity extends Activity {

	TextView location;
	Button refresh;
	WifiIni wifiIni;
	EditText setLokaal;
	private String classroom;
	private String prefTemp;
	
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		Intent msgIntent = new Intent(this, WifiLoc.class);
		startService(msgIntent);
		Intent msIntent = new Intent(this, GetServer.class);
		startService(msIntent);
		
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		refresh = (Button) findViewById(R.id.bRefresh);
		setLokaal = (EditText) findViewById(R.id.etLokaal);
		System.out.println("22");
		//wifiIni = new WifiIni(this);
		System.out.println("33");
		
		//wifiIni.execute();
		System.out.println("44");
		
		//location.append(wifiIni.getMac());

		refresh.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) 
			{
				classroom = setLokaal.getText().toString();
				
				
				init();
				//location.append(" " +  wifiIni.getMac());
				System.out.println(" " + classroom);
			
			}

		});

	}
	 
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.main, menu);
	    return super.onCreateOptionsMenu(menu);
		
	}
	
	public void init(){
		wifiIni = new WifiIni(this, classroom);
		wifiIni.execute();
	}
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	    // Handle presses on the action bar items
	    switch (item.getItemId()) {
	        case R.id.action_friends:
	            System.out.println("action_friends pressed");
	            return true;
	        case android.R.id.home:
	        	System.out.println("Home_pressed");
	        	Intent myIntent = new Intent(this, Climate.class);
	        	startActivity(myIntent);
	        	
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}

	 
}
