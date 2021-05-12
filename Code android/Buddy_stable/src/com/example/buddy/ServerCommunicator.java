package com.example.buddy;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
//import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;




import android.content.Context;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiManager;
import android.os.AsyncTask;
import android.util.Log;
import android.view.View;
import android.widget.TextView;

public class ServerCommunicator {
	public ServerCommunicator(MainActivity context) {
		this.mainContext = context;

		// TODO Auto-generated constructor stub
	}
	final boolean testMode = false;
	private Context mContext;
	private String ownMac;
	private String mac;
	private MainActivity mainContext;
	
	private int timesScanned = 0, mostMacsScanned = 0;
	private String b;
	private String name;
	private int debugInt=0;

	private List<ScanResult> bestMacScan;

	




	 
	public void httpPostPrefTemp(){
		HttpClient httpclient = new DefaultHttpClient();
		HttpPost httppost = new HttpPost(
				"https://oege.ie.hva.nl/~frisw001/iot/temps.php");

		try {// post to server
			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(
					2);
			nameValuePairs.add(new BasicNameValuePair(
					"clients_stemmen_mac", "" + Storage.getName()));
			nameValuePairs.add(new BasicNameValuePair(
					"clients_stemmen_temp", "" + Storage.getPrefTemp()));
			httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			// Execute HTTP Post Request
			HttpResponse response = httpclient.execute(httppost);
		} catch (Exception e) {
		}
	}
	
	
	
	public void httpPostLocation() {
		// Create a new HttpClient and Post Header
		// name = Storage.getOwnInfo().getName();
		
		WifiManager wifi = (WifiManager) mainContext.getSystemService(Context.WIFI_SERVICE);
			mac = "";
			wifi.startScan();
				mostMacsScanned = wifi.getScanResults().size();
				bestMacScan = wifi.getScanResults();
			
				if (mostMacsScanned > 3) {

				// verander bestScan in een string
				System.out.println("amount of MAC-adresses: "
						+ bestMacScan.size());
				for (int i = 0; i < bestMacScan.size(); i++) {
					ScanResult scanResult = bestMacScan.get(i);

					if (scanResult.SSID.equals("eduroam")) {
						b = "" + scanResult.BSSID + scanResult.level;
						if (!b.contains("f-")) {
							mac = mac + b + " ";
							// mac = mac + "" + scanResult.BSSID +
							// scanResult.level + " ";
						

						}
					}
				}

				HttpClient httpclient = new DefaultHttpClient();
				HttpPost httppost = new HttpPost(
						"https://oege.ie.hva.nl/~frisw001/iot/iamhere.php");

				try {// post to server
					List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(
							2);
					nameValuePairs.add(new BasicNameValuePair(
							"clients_ik_hier_mac", "" + "damm006"));
					nameValuePairs.add(new BasicNameValuePair(
							"clients_ik_hier_locatie", "" + mac));
					httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

					// Execute HTTP Post Request
					HttpResponse response = httpclient.execute(httppost);
				} catch (Exception e) {
				}
				
				
			}

		
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
				return new Person("000", "000", 000, true);
		} else {

			String url = ("https://oege.ie.hva.nl/~frisw001/iot/whereis.php?clients_waar_is_uid=")
					+ friendID;
			String friendString = httpGet(url);
			Log.d("see", ""+ friendString);
			// strip response
			String loc = "";
			String temperature = "";
			boolean online = false;
			int endParam = 0;

			char delimiter = ' ';
			int counter = 0;
			for (int i = 0; i < friendString.length(); i++) {
				char c = friendString.charAt(i);
				System.out.print(c);
				if (c == delimiter) {
					switch (counter) {
					case 0:
						loc = friendString.substring(0, i);
						break;
					case 1:
						temperature = friendString.substring(endParam + 1, i);
						break;
//					case 2:
//						temperature = friendString.substring(endParam + 1, i);
//						if (friendString
//								.substring(i + 1, friendString.length())
//								.equals("true")) {
//							online = true;
//						}
//						break;
					}
					counter++;
					endParam = i;
				}

			}
			return new Person(friendID, loc, Double.parseDouble(temperature), online);
		}
	}

	

}
