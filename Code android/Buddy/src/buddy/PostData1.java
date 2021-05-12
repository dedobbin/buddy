package buddy;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import android.os.AsyncTask;

public class PostData1 extends AsyncTask<String, Void, Void> {

	private String ownmac;
	private String mac;

	public PostData1(String a, String b) {
		this.ownmac = a;
		this.mac = b;
	}

	@Override
	protected Void doInBackground(String... urls) {

		System.out.println(" in execute");
		// Create a new HttpClient and Post Header
		HttpClient httpclient = new DefaultHttpClient();
		HttpPost httppost = new HttpPost(
				"https://oege.ie.hva.nl/~frisw001/iot/room_collection.php");

		try {
			// Add your data
			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
			nameValuePairs.add(new BasicNameValuePair("server_rooms_ruimte", ""
					+ ownmac));

			nameValuePairs.add(new BasicNameValuePair("server_rooms_waardes",
					"" + mac));
			httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			// Execute HTTP Post Request
			HttpResponse response = httpclient.execute(httppost);

		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block

		} catch (IOException e) {
			// TODO Auto-generated catch block
		}
		return null;
	}

	protected void onPostExecute(Void param) {
		System.out.println(" 1234");

	}
}
