package com.example.buddy;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiManager;
import android.os.AsyncTask;

public class WifiLoc extends IntentService {

	private String ownMac;
	private String mac;
	private int timesScanned, mostMacsScanned=0;
	private String b;
	private String name;
	private List<ScanResult> bestMacScan;
	public WifiLoc() {
		super("Wifiloc");
	}
	

	@Override
	protected void onHandleIntent(Intent intent)
	{
	name="damm006";
		WifiManager wifi = (WifiManager) getSystemService(Context.WIFI_SERVICE);
		ownMac = wifi.getConnectionInfo().getMacAddress();

		while (true) {
			mac = "";

			wifi.startScan();
			//List<ScanResult> wifiList = wifi.getScanResults();

			//eerst stuurde het nu naar server, en sleep voor 10000 ms.
			//nu doet eerst check, om 2000 ms, en stuurt pas als timesScanned > 5, de grootste
			if (wifi.getScanResults().size() > mostMacsScanned){//check if recent scan found more accespoints than previous scans
			mostMacsScanned = wifi.getScanResults().size();	
			bestMacScan = wifi.getScanResults();
			}

				try {
					Thread.sleep(1000);
				} catch (InterruptedException e) {
			}
				this.timesScanned++;
			if (timesScanned > 10){
				//verander bestScan in een string
				System.out.println("amount of MAC-adresses: "+ bestMacScan.size());
				for (int i = 0; i < bestMacScan.size(); i++) {
					ScanResult scanResult = bestMacScan.get(i);

					if (scanResult.SSID.equals("eduroam")) {
						b=""+ scanResult.BSSID + scanResult.level;
						if(!b.contains("f-")){
						mac= mac + b + " ";	
						//mac = mac + "" + scanResult.BSSID + scanResult.level + " ";
						
						}
					}
				}
				
				HttpClient httpclient = new DefaultHttpClient();
				HttpPost httppost = new HttpPost(
						"https://oege.ie.hva.nl/~frisw001/iot/iamhere.php");
 
				try {//post to server
					List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(
							2);
					nameValuePairs.add(new BasicNameValuePair(
							"clients_ik_hier_mac", "" + name));
					nameValuePairs.add(new BasicNameValuePair(
							"clients_ik_hier_locatie", "" + mac));
					httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

					// Execute HTTP Post Request
					HttpResponse response = httpclient.execute(httppost);
				} catch (Exception e) {
				}
				timesScanned =0;
			}
		}
	}
}
