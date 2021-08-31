<?php

    class Narvesen {

        private array $groceryList = [];

        public function listOfGroceries(string $name, int $price, int $quantity){
            $this->groceryList[] = [$name, $price, $quantity];
        }

        public function getGroceryList(): array {
            return $this->groceryList;
        }

        public function setGroceryList(int $productKey, int $quantity) {
            $this->groceryList[$productKey][2] = $quantity;
        }

    }

    class ShoppingCart {
        private array $cart = [];
        private array $total = [];
        private int $totalItemCost = 0;
        private Buyer $buyer;

        public function __construct(Buyer $person) {
            $this->buyer = $person;
        }

        public function setCart(string $product, int $quantity) {
            $this->cart += [$product => $quantity];
        }

        public function setTotal(string $product, int $price) {
            $this->total += [$product => $price];
        }

        public function viewCart($input): string {
            $displayCart = "";
            $totalCost = 0;
            if(strtoupper($input) === "C"){
                if(!$this->cart){
                    return "Cart is empty!\n";
                }
                $displayCart .= "\nCash: {$this->buyer->getCash()}\n\n";
                foreach($this->cart as $index => $product){
                    $displayCart .= "$index Qty: $product | Cost - \${$this->total[$index]}\n";
                    $totalCost += $this->total[$index];
                }
            }
            $this->totalItemCost = $totalCost;
            return $displayCart . "Total Cost: \$$totalCost\n\n";
        }

        public function canYouBuy(): string {
            if($this->totalItemCost === 0){
                return "Cart is empty. You didn't buy anything!";
            }
            if($this->totalItemCost <= $this->buyer->getCash()) {
                $this->buyer->setCash($this->totalItemCost);
                return "Thank you for the purchase!";
            }
                return "Not enough money. Purchase cannot be completed!";
        }

    }

    class Buyer {

        private string $name;
        private int $cash;

        public function __construct(string $name, int $cash){
            $this->name = $name;
            $this->cash = $cash;
        }

        public function getName(): string {
            return $this->name;
        }

        public function getCash(): int {
            return $this->cash;
        }

        public function setCash($input){
            $this->cash = $this->cash - $input;
        }

    }

    echo "Welcome to the shop\n";

    $name = readline("Enter your name: ");
    $cash = (int) readline("Cash: ");

    $person = new Buyer($name, $cash);
    echo "Hello, {$person->getName()}! Cash: \${$person->getCash()}\n\n";
    echo "Type c anytime within the program to view your shopping cart!\n";
    echo "Type q anytime within the program to Exit!\n\n";

    $cart = new ShoppingCart($person);
    $narvesen = new Narvesen();
    $narvesen->listOfGroceries("Apple", 5, 50);
    $narvesen->listOfGroceries("Banana", 6, 20);
    $narvesen->listOfGroceries("Onion", 2, 32);
    $narvesen->listOfGroceries("Soul", 25, 10);
    $narvesen->listOfGroceries("Narrowband Filter", 26, 100);
    $narvesen->listOfGroceries("Glock", 17, 75);



    while(true){

        foreach($narvesen->getGroceryList() as $index => $grocery){
            echo "$index | Product: $grocery[0], Price: \$$grocery[1], Quantity: $grocery[2]\n";
        }

        $product = readline("Choose a product: ");

        exitProgram($product);
        if(strtoupper($product) === "C"){
            shoppingCartMenu($product, $cart, $person);
            $product = readline("Choose a product: ");
        }


            if(!isset($narvesen->getGroceryList()[$product]) || !is_numeric($product)) {
                    echo "Invalid input\n";
                    continue;
            }

            $product = (int) $product;

            echo $narvesen->getGroceryList()[$product][0] .
                " $" . $narvesen->getGroceryList()[$product][1] .
                " [" . $narvesen->getGroceryList()[$product][2]. "]\n";

            $cartInput = readline("Choose quantity you wanna buy: ");

            exitProgram($cartInput);
            if(strtoupper($cartInput) === "C"){
                shoppingCartMenu($cartInput, $cart, $person);
                continue;
            }

            if(!is_numeric($cartInput)){
                echo "Wrong input!\n";
                continue;
            }

            $cartInput = (int) $cartInput;

            if($narvesen->getGroceryList()[$product][2] < $cartInput){
                echo "Not enough items in stock\n";
                continue;
            }

            $removeFromStock = $narvesen->getGroceryList()[$product][2] - $cartInput;
            $narvesen->setGroceryList($product, $removeFromStock);
            $cart->setCart($narvesen->getGroceryList()[$product][0], $cartInput);
            $total = $narvesen->getGroceryList()[$product][1] * $cartInput;
            $cart->setTotal($narvesen->getGroceryList()[$product][0], $total);
            echo "Product added to the cart!\n";

    }

    function shoppingCartMenu(string $input, ShoppingCart $cart, Buyer $person){
        $isPromptActive = true;
        while($isPromptActive){
            echo $cart->viewCart($input);
            $buy = readline("Do you want to buy your items? (Y/N) ");
            switch(strtoupper($buy)){
                case "Y":
                    $message = $cart->canYouBuy() . "\nYou left with \${$person->getCash()}!";
                    die($message);
                case "N":
                    $isPromptActive = false;
                    break;
                default:
                    echo "Wrong input!\n";
                    break;
            }
        }
    }

    function exitProgram(string $input){
        if(strtoupper($input) === "Q"){
            die("Bye!");
        }
    }

