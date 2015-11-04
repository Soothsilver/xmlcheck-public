<?php
$array = [$columnName -> $name];
/*
use asm\core\Repositories;
use asm\core\User;

require_once 'www/vendor/autoload.php';
\asm\core\Config::init('www/core/config.ini', 'www/core/internal.ini');

die (print_r(\asm\core\Config::get('paths'), true));

die (realpath(\asm\utils\Filesystem::combinePaths(__DIR__, "..")));

User::instance()->login('Soothsilver', 'Militia7*');

try {
    $ratings = Repositories::getEntityManager()->createQuery(
"SELECT s, SUM(s.rating) AS rating, a, g.id, g.name, g.description FROM \Submission s JOIN s.assignment a JOIN a.group g WHERE s.user = :userId GROUP BY g, g.name"
    )->setParameter('userId', User::instance()->getId())->getResult();
} catch (\Doctrine\ORM\Query\QueryException $queryException) {
    echo "<u>" . $queryException->getMessage() . "</u>";
}
   \Doctrine\Common\Util\Debug::dump($ratings);
*/

/*import java.util.Objects;

public class Frost {
    public static void main(String[] args)
    {
        String a = "AAA";
        String b = "BBB";
        long time = System.nanoTime();
        for (int i = 0; i < 100000000; i++)
        {
            if (!Objects.equals(a + b, "AAABBB"))
            {
                System.out.println("ERROR");
            }
        }
        System.out.println("OK");
        System.out.println("Time taken: " + (System.nanoTime() - time)/1000000 + " milliseconds");
    }
}
*/