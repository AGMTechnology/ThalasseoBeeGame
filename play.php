<?php

class Bee
{
    private $beeType = NULL;
    private $quantity = 0;
    private $lifeSpan = 0;

    public static $availableTypes = array(
        'queen' => array(
            'hit'     => 15,
            'lifespan' => 100
        ),
        'worker' => array(
            'hit'     => 20,
            'lifespan' => 50
        ),
        'scout' => array(
            'hit'     => 15,
            'lifespan' => 30
        )
    );

    public function __construct($beeType, $quantity)
    {
        // Créer une nouvelle collection d'abeilles

        // Ce type d'abeille existe-t-il ?
        if (array_key_exists($beeType, self::$availableTypes))
        {
            // Définir les propriétés par défaut
            $this->beeType = $beeType;
            $this->quantity = $quantity;
            $this->lifeSpan = self::$availableTypes[$beeType]['lifespan'];
        } else {
            throw new Exception('Les abeilles doivent être une Reine, une Ouvrière ou un Mâle');
        }
    }

    public function getType()
    {
        return $this->beeType;
    }

    public function getLifeSpan()
    {
        return $this->lifeSpan;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}

class Game
{
    public $message;

    public function __construct()
    {
        session_start();
    }

    public function resetGame($suppressMessage = FALSE)
    {
        // Réinitialiser chaque type d'abeilles
        $bee = new Bee('queen', 1);
        $this->addBees($bee);

        $bee = new Bee('worker', 5);
        $this->addBees($bee);

        $bee = new Bee('scout', 8);
        $this->addBees($bee);

        $_SESSION['turn'] = 0;

        if (!$suppressMessage) {
            $this->message = 'Le jeu a été réinitialisé. Toutes les abeilles sont réapparues.';
        }
    }

    public function addBees(Bee $newBee)
    {
        $_SESSION['bees'][$newBee->getType()] = array(
            'quantity'    => $newBee->getQuantity(),
            'life'        => $newBee->getLifeSpan(),
            'hitPoints'   => Bee::$availableTypes[$newBee->getType()]['hit']
        );
    }

    public function hit()
    {

        $bees = (array)$_SESSION['bees'];
        $turn = $_SESSION['turn'];

        // La session est-elle toujours valide ?
        if ($bees) {
            // Obtenir une abeille aléatoire de la session
            $seed = array_rand($bees, 1);

            // Vérifier s'il reste des abeilles du type sélectionné
            if ($bees[$seed]['life'] == 0 && $bees[$seed]['quantity'] == 0) {
                // Appliquer le coup à nouveau
                $this->hit();
            }

            // Déduire les points de coup appropriés de l'abeille sélectionnée
            $bees[$seed]['life'] -= $bees[$seed]['hitPoints'];

            // Si cette abeille n'a plus de vie
            if ($bees[$seed]['life'] < 0) {
                // Réinitialiser sa vie à la durée de vie désignée
                $bees[$seed]['life'] = Bee::$availableTypes[$seed]['lifespan'];

                // Réduire la quantité d'abeilles de 1
                $bees[$seed]['quantity'] -= 1;
            }

            // Combien d'abeilles restent pour ce type
            if ($bees[$seed]['quantity'] > 0) {
                // Il reste au moins une abeille
                $this->message ='L\'abeille de type ' . ucfirst($seed) . ' a été touchée. Il en reste ' . $bees[$seed]['quantity'] . '.';
            } else // Non, soit la reine est morte, soit toutes les ouvrières sont mortes, soit tous les scouts sont morts
            {
                switch ($seed) {
                    case 'queen': // La reine est morte, fin du jeu
                        $this->message = 'La reine est morte, toutes les abeilles meurent ! Le jeu se relance.';
                        $this->resetGame(TRUE);
                        return;
                        break;

                    default:
                        $bees[$seed]['life'] = 0;
                        $this->message = ucfirst($seed) . ' a été touché et est morte. Les abeilles de type ' . $seed . ' sont exterminées.';
                        break;
                };
            }

            // Mettre à jour les données de session avec les changements
            $_SESSION['bees'][$seed] = $bees[$seed];

            $_SESSION['turn'] = $turn + 1;


        } else // La session n'existe plus
        {
            $this->resetGame();
        }
    }

    public function showView()
    {
        // Définir les données de la vue
        $view = new stdClass();
        $view->message = $this->message;
        $view->queen = $_SESSION['bees']['queen'];
        $view->worker = $_SESSION['bees']['worker'];
        $view->drone = $_SESSION['bees']['scout'];
        $view->turn = $_SESSION['turn'];

        include 'BeeGameUI.php';
    }
}

$gameController = new Game();

if ($_POST) {
    $gameController->hit();
    $gameController->showView();
} else {
    $gameController->message = 'Le jeu a été réinitialisé.';
    $gameController->resetGame();
    $gameController->showView();
}
