-   [x] Rate Limit, Each user can do 2 transactions per 5 seconds.
-   [x] Each Phone Number is able to have many accounts. (Just one phone number.)

-   [x] Add a limit to project, everyday each account (card) is able to make 50M (the total amount) transactions. (Write middleware for it.) (Store total amount of transactions of each card in redis and set expire time to 12 A.M.)

-   [x] Write an API to get cash. (on this transaction system have to create a new row in transactions table then the card and user balance decrease.)

-   [x] Write an API to return total balance and balance of each account (card).

-   [x] Write an API to return top 3 users they had most transactions. (store all transactions and it amount in redis using setex then when api called get total amount of each user and select the 3 tops, set 10 minutes as expire time.)

-   [x] Write an API to transfer money between two accounts.
-   [x] For all endpoints write custom request.

-   [ ] Customize README File.
-   [ ] Make all variable and function names camel case.
