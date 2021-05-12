package buddy;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import android.content.Context;
import android.net.wifi.ScanResult;
import android.net.wifi.WifiManager;
import android.os.AsyncTask;

public class WifiIni extends AsyncTask<Void, String, Void> {

 private Context mContext;
 private String lokaal;
 private String mac, b;

 public WifiIni(Context context, String lokaal) {
  this.mContext = context;
  this.lokaal = lokaal;
 }

 @Override
 protected Void doInBackground(Void... params) {

  WifiManager wifi = (WifiManager) mContext
    .getSystemService(Context.WIFI_SERVICE);
  mac = "";
  
  System.out.println(lokaal);

  wifi.startScan();
  List<ScanResult> wifiList = wifi.getScanResults();
  for (int i = 0; i < wifiList.size(); i++) {
   ScanResult scanResult = wifiList.get(i);
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
    "https://oege.ie.hva.nl/~frisw001/iot/room_collection.php");

  try {
   // Add your data
   List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
   nameValuePairs.add(new BasicNameValuePair("server_rooms_ruimte", ""
     + lokaal));
   nameValuePairs.add(new BasicNameValuePair("server_rooms_waardes",
     "" + mac));
   httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

   // Execute HTTP Post Request
   HttpResponse response = httpclient.execute(httppost);
   publishProgress(response.toString());
  } catch (Exception e) {
  }

  return null;
 }

 @Override
 protected void onProgressUpdate(String... values) {
  super.onProgressUpdate(values);
  System.out.println(values[0]);
 }

}
