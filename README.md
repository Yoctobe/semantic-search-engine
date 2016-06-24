# semantic-search-engine
A simple english semantic search engine php script.

# How it works
This script allows you to generate synonym queries from the main search query using a Wordnet API. For each generated query the script analyses the relevance and rank every result. Finally search results are sorted by relevance order, they will seem covering all possible interesting products even if the customer didn't send the right keyword.

# Connect to the database
In this example, you need to create a mysql database and change the value "DATABASE" in the index.php file; than if you want to test this example you need to create also a table and name it "products".

The products tables should contain three columns : ID, product_title, product description. (picture : https://goo.gl/photos/Eh4tWmXyhbdqRVoR6)

You can modify the mysql search query add or remove parameters etc depending on the structure of the data tables you have.
