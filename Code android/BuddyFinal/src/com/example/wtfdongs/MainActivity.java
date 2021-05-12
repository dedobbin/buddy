package com.example.wtfdongs;

import java.util.Timer;
import java.util.TimerTask;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.TextView;

/**
 * Hoe stelt gebruiker in wat zijn favo temperatuur is?
 * 
 * 
 * wifiloc gescande info, en fav temp
 * 
 * main switch, main je eigen info
 * 
 * begin storage alles in 1 setten
 * 
 * 
 */
public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		// set Storage vars
		int prefTemp = 20;
		final String name = "ik001";

		// store all vars
		Storage.setPrefTemp(prefTemp);
		Storage.setName(name);
		String[] friends = { "1", "2", "3", "4" };
		Storage.setFriendIDs(friends);

		// starts communication with server, keeps constantly repeating
		final Handler handler = new Handler();
		Timer timer = new Timer();
		TimerTask doAsynchronousTask = new TimerTask() {
			@Override
			public void run() {
				handler.post(new Runnable() {
					public void run() {
						try {
							ServerCommunicator contCommunication = new ServerCommunicator();
							contCommunication.execute();
							TextView ownInfo = (TextView) findViewById(R.id.ownInfoTV);
							ownInfo.setText(Storage.getOwnInfo().getRoom());
						} catch (Exception e) {
							Log.d("main",
									"communication with server thread failed");
						}
					}
				});
			}
		};
		timer.schedule(doAsynchronousTask, 0, 10000);

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public void changeActivity(View view) {
		Intent intent = new Intent(this, ClimateActivity.class);
		switch (view.getId()) {
		case R.id.gotoClimate:
			intent = new Intent(this, FriendListActivity.class);
			break;
		case R.id.gotoFriendList:
			intent = new Intent(this, FriendListActivity.class);
			break;
		}
		startActivity(intent);

	}

}
