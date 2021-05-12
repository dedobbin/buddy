package com.example.buddy;


import java.util.Timer;
import java.util.TimerTask;

import com.example.buddy.WifiIni;

import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.app.Activity;
import android.content.Intent;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
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
	private int prefTemp, prefHum;
	final String name="damm006";
	TextView ownClass;
	TextView ownTemp;
	int debugInt=0;
	ServerCommunicator servercom = new ServerCommunicator(this);
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		getActionBar().setDisplayHomeAsUpEnabled(true);
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		refresh = (Button) findViewById(R.id.bRefresh);
		setLokaal = (EditText) findViewById(R.id.etLokaal);
		ownClass = (TextView) findViewById(R.id.tvOwnLocation);
		ownTemp = (TextView) findViewById(R.id.tvOwnTemp);
		prefTemp = 20;
		prefHum = 1;
		
		

		
		// store all vars
		Storage.setPrefTemp(prefTemp);
		Storage.setPrefHum(prefHum);
		Storage.setName(name);
		String[] friends = { "damm006", "frisw001", "damm006", "frisw001" };
		Storage.setFriendIDs(friends);
		// starts communication with server, keeps constantly repeating
		final Handler handler = new Handler();
	    Timer timer = new Timer();
	    TimerTask doAsynchronousTask = new TimerTask() {       
	        @Override
	        public void run() {
	        	Log.d("main update loop", "started");
	            handler.post(new Runnable() {
	                public void run() {       
	                    try {
	                        AsyncTask updateFriendList = new AsyncTask(){

								@Override
								protected Object doInBackground(
										Object... params) {
									int prefTemp = Storage.getPrefTemp();
									 //httpPost("https://oege.ie.hva.nl/~frisw001/iot/iamhere.php");//send
									// temperature
									servercom.httpPostLocation();
									servercom.httpPostPrefTemp();
									// send here iam
									Person self = servercom.friendInfoToPerson(Storage.getName());// get own info (for
																						// location)
									Storage.setOwnInfo(self);

									Person[] friends = new Person[Storage.getFriendIDs().length];
									for (int i = 0; i < Storage.getFriendIDs().length; i++) {
										// translation from name to ID if needed
										friends[i] = servercom.friendInfoToPerson(Storage.getFriendIDs()[i]);
									}
										//if no response from server, fake list is made
										if (friends[0] == null){
											debugInt++;
											 Person[]debugList = {new Person("fake1", "fakeloc1", debugInt, true), new Person("fake2", "fakeloc2", 02, true), new Person("fake3", "fakeloc3", 03, true)};
											 friends = debugList;
										}
										// will put info from server (friends) into containers on screen
										Log.d("servercom", "tries to set livefriends");
										Storage.setLiveFriendsList(friends);
									return null;
								}

								@Override
								protected void onPostExecute(Object result) {
									debugInt++;
							         TextView ownlocation = (TextView)  findViewById(R.id.tvOwnLocation);        
							         TextView owntemproom = (TextView)  findViewById(R.id.tvOwnTemp);
							         ownlocation.setText("" + Storage.getOwnInfo().getRoom());
							         owntemproom.setText("" + Storage.getOwnInfo().getTempInRoom() + " °C");
							         Log.d("ServerCommunication",
												"onPostExecute");
							         Log.d("gui update", "main");
								}
	                        	
	                        };
	                        updateFriendList.execute();
	                    } catch (Exception e) {
	                        // TODO Auto-generated catch block
	                    }
	                }
	            });
	        }
	    };
	    timer.schedule(doAsynchronousTask, 0, 5000); 
	


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
	        	Intent myIntent = new Intent(this, FriendListActivity.class);
	        	startActivity(myIntent);
	            return true;
	        case android.R.id.home:
	        	System.out.println("Home_pressed");
	        	Intent mIntent = new Intent(this, Climate.class);
	        	startActivity(mIntent);
	        	
	        default:
	            return super.onOptionsItemSelected(item);
	    }
	}

	 
}
