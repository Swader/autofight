#Autofight

This is a small task I was given by a prospective employer. Originally, I was supposed to only make two armies fight each other automatically with a little element of randomness, but I guess I got carried away and played around with it more than I had to.

This is a completely useless no-GUI highly extensible text output "app".
Use it only for educational purposes on OOP and how to write clean and neatly commented code.

##What's going on here?
First, two armies are generated with the number of units you give them. The armies have a random number of random types of units - one might be 100% infantry, the other might have 10 tanks alongside 40 infantry, and so on. There is support for more than 2 armies, but you need to manually add the army to the War object in index.php. Then, each army is given a randomly generated label, unless you hard code one, again in index.php. On every turn, the army order is randomized. This simulates initiative. An army picks the army to attack if there's more than two in the war (if not, it just picks the one that's not them), and all soldiers from the attacking army then make a move against the defending army, one by one, targeting a random alive opponent. Once they've all done their moves, the next army does the same - until there are no more armies left to move that turn.

The "game" ends when all armies but one have zero survivors. There can be only one :)

This is what the fight looks like:

<a href="http://showterm.io/a0616ce5e6f411f292e18#fast" title="Sample 10 vs 10 fight">Sample 10 vs 10 fight</a>

###Randomness
There's an element of randomness in the game. Some of these random aspects are as follows:

- a tank has 10% as much chance of appearing in an army as an infantry unit does. In other words, infantry units are 10 times more common.
- a unit can miss, and if they miss in a specific miss range, can accidentally hit someone else
- there's chance of critical hits, and critical misses. Critical hits do 5x damage, and critical misses permanently reduce either health or accuracy of the unit. A critical miss is akin to someone shooting themselves in the foot, or a projectile getting stuck in the tank's turret.
- an infantry unit has 0.0001% chance of going insane. When insane, the unit may choose skip his turn, attack a fellow unit, or even commit suicide.
- when no labels are given, armies get randomly generated names from hard coded adjectives and nouns. This makes for some interesting combinations like "Black Death" or "Lonely Marauders".
- tanks have splash damage. If they score a hit, neighboring units suffer shrapnel damage.

##Usage
You can test it out in CLI mode or via a web interface.
In CLI, just run index.php X Y where X and Y are sizes of the first and second army respectively (e.g. php index.php 50 50). The army is auto-generated with the available units, and you should automatically start seeing output in your terminal, turn by turn. In the browser, the same applies, only the output is calculated and printed out at once, and you need to use a link like index.php?army1=50&army2=50.

##Requirements
You need at least PHP 5.4 because the short array syntax is used and some other 5.4ish stuff.

##Fooling around and extending
You can add new unit types, just implement the Unit interface or extend the Unit abstract. You can also modify the index.php file to accept more than 2 armies, the code already supports it. If you do mess with it, let me know what you end up with. I'm not really happy with how the logging turned out, I'd like something more flexible but didn't have time to fix it. PRs appreciated.

For example, to implement a Medic class unit, you would extend the Unit abstract, and write an "act" method such that a fellow army unit is selected, and his Health increased depending on rolled score.
To implement a different kind of logger - for example one that logs the result into a file, just implement the "BattleLogger" interface and plug it into the War object in index.php instead of the current one.
