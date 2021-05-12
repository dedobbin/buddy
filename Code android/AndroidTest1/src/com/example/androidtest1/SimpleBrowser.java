package com.example.androidtest1;

import android.app.Activity;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class SimpleBrowser extends Activity{

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.simplebrowser);
		
		WebView ourBrow = (WebView) findViewById(R.id.wvBrowser);
		ourBrow.loadUrl("http://www.google.com");
		ourBrow.setWebViewClient(new WebViewClient());
		
	}

	
	
}
