package com.example.buddy;

import java.util.Timer;
import java.util.TimerTask;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.widget.TextView;

public class FriendListActivity extends Activity  {
	ServerCommunicator serverCommunicator;
	int[][] graphicsFriendList ;
	Person[] liveFriendList;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		try{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.friendlist);
		

		// check who are inFriendlist of account
		//final String[] innerFriendList = Storage.getFriendIDs();//sends friend ids, to get actual info
		int[][] friendListGraphicElements = getScreenElements();
		updateScreenInfo(Storage.getLiveFriendsList());
		
		final Handler handler = new Handler();
	    Timer timer = new Timer();
	    TimerTask doAsynchronousTask = new TimerTask() {       
	        @Override
	        public void run() {
	        	Log.d("friendlist update loop", "started");
	            handler.post(new Runnable() {
	                public void run() {       
	                    try {
	                        AsyncTask updateFriendList = new AsyncTask(){

								@Override
								protected Object doInBackground(
										Object... params) {
									return null;
								}

								@Override
								protected void onPostExecute(Object result) {
									updateScreenInfo(Storage.getLiveFriendsList());
									 Log.d("gui update", "friendslist");
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
		}
		catch(Exception offline){
			Log.d("friendlist problems","offline?");
		}
		

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public int[][] getScreenElements() {
		// these are all containers, the info for the friends will be inserted
		// should be more dynamicly
		graphicsFriendList = new int[4][4];
		graphicsFriendList[0][0] = (R.id.FriendOne);
		graphicsFriendList[1][0] = (R.id.FriendOneLoc);
		graphicsFriendList[2][0] = (R.id.FriendOneTemp);
		graphicsFriendList[3][0] = 0;

		graphicsFriendList[0][1] = (R.id.FriendTwo);
		graphicsFriendList[1][1] = (R.id.FriendTwoLoc);
		graphicsFriendList[2][1] = (R.id.FriendTwoTemp);
		graphicsFriendList[3][1] = 0;

		graphicsFriendList[0][2] = (R.id.FriendThree);
		graphicsFriendList[1][2] = (R.id.FriendThreeLoc);
		graphicsFriendList[2][2] = (R.id.FriendThreeTemp);
		graphicsFriendList[3][2] = 0;

		graphicsFriendList[0][3] = (R.id.FriendFour);
		graphicsFriendList[1][3] = (R.id.FriendFourLoc);
		graphicsFriendList[2][3] = (R.id.FriendFourTemp);
		graphicsFriendList[3][3] = 0;

		return graphicsFriendList;
	}

	public void updateScreenInfo( Person[] friends) {
		for (int i = 0; i < friends.length; i++) {
			Person friend = friends[i];
			((TextView) findViewById(graphicsFriendList[0][i])).setText(friend
					.getName());

			((TextView) findViewById(graphicsFriendList[1][i])).setText(friend
					.getRoom());

			((TextView) findViewById(graphicsFriendList[2][i])).setText(friend
					.getTempInRoom() + "");

		}

	}
	


}