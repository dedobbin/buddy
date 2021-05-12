package com.example.wtfdongs;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;
import android.util.Log;

public class ServerCommunicator  extends AsyncTask  {
	final boolean testMode = true;

	@Override
	protected Object doInBackground(Object...in ) {
				
				int prefTemp = Storage.getPrefTemp();
				httpPost("url"+Storage.getPrefTemp()+"");//send temperature
				httpPost("url");//send here iam
				Person self = friendInfoToPerson(Storage.getName());//get own info (for location)
				Storage.setOwnInfo(self);
				
				Person[] friends = new Person[Storage.getFriendIDs().length];
				for (int i = 0; i < Storage.getFriendIDs().length; i++) {
					// translation from name to ID if needed
					friends[i] = friendInfoToPerson(Storage.getFriendIDs()[i]);
					// will put info from server (friends) into containers on scren
					// (friendListGraphicElements)
					Storage.setLiveFriendsList(friends);
					
				}
				
		return null;
	}



	public void httpPost(String url) {
	    // Create a new HttpClient and Post Header
	    Log.d("this httppost message", "DOES noting");
	} 

	public String httpGet(String url) {
		String httpResponseString = "";
		try {

			InputStream inputStream = null;
			HttpClient httpclient = new DefaultHttpClient();
			HttpResponse httpResponse = httpclient.execute(new HttpGet(url));
			inputStream = httpResponse.getEntity().getContent();

			if (inputStream == null)
				return null;// didnt work,
							// should throw
							// exception
			else {
				BufferedReader bufferedReader = new BufferedReader(
						new InputStreamReader(inputStream));
				String line = "";
				while ((line = bufferedReader.readLine()) != null)
					httpResponseString += line;
				inputStream.close();
			}
		} catch (Exception e) {
			Log.d("ServerCommunicator", e.toString());
		}
		return httpResponseString;
	}

	public Person friendInfoToPerson(String friendID) {
		// test mode

		if (testMode) {
			if (friendID.equals("2"))
				return new Person("cock", "E444", 22, true);
			else if (friendID.equals("1"))
				return new Person("naam2", "E133", 250, true);
			else if (friendID.equals("3"))
				return new Person("naam3", "E133", 250, true);
			else if(friendID.equals("ik001"))
				return new Person("ik", "E123", 20000, true);
			else
				return new Person("000", "000", 000, true);
		} else {

			String url = ("https://oege.ie.hva.nl/~frisw001/iot/whereis.php?clients_waar_is_uid")
					+ friendID;
			String friendString = httpGet(url);
			// strip response
			String name = "";
			String loc = "";
			String temperature = "";
			boolean online = false;
			int endParam = 0;

			char delimiter = '+';
			int counter = 0;
			for (int i = 0; i < friendString.length(); i++) {
				char c = friendString.charAt(i);
				System.out.print(c);
				if (c == delimiter) {
					switch (counter) {
					case 0:
						name = friendString.substring(0, i);
						break;
					case 1:
						loc = friendString.substring(endParam + 1, i);
						break;
					case 2:
						temperature = friendString.substring(endParam + 1, i);
						if (friendString
								.substring(i + 1, friendString.length())
								.equals("true")) {
							online = true;
						}
						break;
					}
					counter++;
					endParam = i;
				}

			}
			return new Person(name, loc, Integer.parseInt(temperature), online);
		}
	}

}
