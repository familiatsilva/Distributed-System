(function()
{
  
	var btnRecovery 	= document.getElementById("btn-recovery");
	var btnLogin 		= document.getElementById("btn-login");
	var inputUsername 	= document.getElementById("input-username");
	var inputPassword	= document.getElementById("input-password");

	function sendAjax(callback, data)
	{
		var request = new XMLHttpRequest();
	    request.open("POST", producerAPI, true);
	    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

	    request.onreadystatechange = function()
	    {
			if (request.readyState === 4)
			{
				if (request.status === 200)
				{
					callback(request.response)
				}
				else
				{
					if(typeof request.response != 'undefined')
					{
						var data = JSON.parse(request.response);
						alert(data.msg);
					}
					else
					{
						alert('Server is not available at this time.');
					}
					
				}
			}	    	
	    };

		var urlEncodedData = "";
		var urlEncodedDataPairs = [];

		for(name in data) {
			urlEncodedDataPairs.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
		}

		urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');

	    request.send(urlEncodedData);
	}

	btnRecovery.addEventListener('click', function()
	{
		if(inputUsername.value.trim() == "")
		{
			alert('The username is required.')
		}
		else
		{
			sendAjax(function(res)
			{
				var result = JSON.parse(res)
				alert(result.msg);
			},
			{
				action: 'recoveryPassword',
				username: inputUsername.value.trim()
			})
		}
	
	}, false);

	btnLogin.addEventListener('click', function()
	{

		if(inputUsername.value.trim() == "" || inputPassword.value.trim() == "")
		{
			alert('The username and password is required.')
		}
		else
		{
			sendAjax(function(res)
			{
				var result = JSON.parse(res)
				alert(result.msg);
			},
			{
				action: 'login',
				username: inputUsername.value.trim(),
				password: inputPassword.value.trim()
			})
		}		

	}, false);

})();