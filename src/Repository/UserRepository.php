<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllUserUms()
    {
      $config = new Configuration();
      $connectionParams = array(
        'dbname' => 'ums',
        'user' => 'sincro',
        'password' => 'Despecho2019**',
        'host' => '192.168.107.14',
        'port' => 3306,
        'charset' => 'utf8',
        'driver' => 'pdo_mysql',
      );

      $dbh = DriverManager::getConnection($connectionParams, $config);
      $qb = $dbh->createQueryBuilder();
      $qb->select('aa.id, aa.first_name, aa.last_name, aa.username')
        ->from('accounts_account', 'aa')
        ->where($qb->expr()->eq('aa.active', ':act'))
        ->setParameter('act', 1);

      return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
//      return $qb->execute();
    }

    public function findAllUserIdUms($user)
    {
      $config = new Configuration();
      $connectionParams = array(
        'dbname' => 'ums',
        'user' => 'sincro',
        'password' => 'Despecho2019**',
        'host' => '192.168.107.14',
        'port' => 3306,
        'charset' => 'utf8',
        'driver' => 'pdo_mysql',
      );

      $dbh = DriverManager::getConnection($connectionParams, $config);
      $qb = $dbh->createQueryBuilder();
      $qb->select('aa.id, aa.username, aa.password, aa.personal_ID,aa.first_name,aa.last_name,accounts_mailaccount.mail_username')
        ->from('accounts_account', 'aa')
        ->innerJoin('aa','accounts_mailaccount','accounts_mailaccount','aa.id = accounts_mailaccount.account_id')
        ->where($qb->expr()->eq('aa.active', ':act'))
        ->andWhere($qb->expr()->eq('aa.id', ':us'))
        ->setParameter('us', $user)
        ->setParameter('act', 1);

      return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
/*consulta original usuarios
 public function findAllUserIdUms($user)
    {
      $config = new Configuration();
      $connectionParams = array(
        'dbname' => 'ums',
        'user' => 'admin',
        'password' => 'MY@dmin.123',
        'host' => 'mysql.camilo.sld.cu',
        'port' => 3306,
        'charset' => 'utf8',
        'driver' => 'pdo_mysql',
      );

      $dbh = DriverManager::getConnection($connectionParams, $config);
      $qb = $dbh->createQueryBuilder();
      $qb->select('aa.id, aa.username, aa.password, aa.personal_ID,aa.first_name,aa.last_name')
        ->from('accounts_account', 'aa')
        ->where($qb->expr()->eq('aa.active', ':act'))
        ->andWhere($qb->expr()->eq('aa.id', ':us'))
        ->setParameter('us', $user)
        ->setParameter('act', 1);

      return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
*/
    public function findDetallesUms($user)
    {
      $config = new Configuration();
      $connectionParams = array(
        'dbname' => 'ums',
        'user' => 'sincro',
        'password' => 'Despecho2019**',
        'host' => '192.168.107.14',
        'port' => 3306,
        'charset' => 'utf8',
        'driver' => 'pdo_mysql',
      );

      $dbh = DriverManager::getConnection($connectionParams, $config);
      $qb = $dbh->createQueryBuilder();
      $qb->select('aa.id, aa.username, aa.personal_ID, aa.first_name, aa.last_name, aa.telephone, ad.name AS dep, aar.name')
        ->from('accounts_account', 'aa')
        ->innerJoin('aa', 'accounts_department', 'ad', 'ad.id = aa.department_id')
        ->innerJoin('ad', 'accounts_area', 'aar', 'aar.id = ad.area_id')
        ->where($qb->expr()->eq('aa.active', ':act'))
        ->andWhere($qb->expr()->eq('aa.id', ':us'))
        ->setParameter('us', $user)
        ->setParameter('act', 1);

      return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findDetallesUmsxusuario($username)
    {
      $config = new Configuration();
      $connectionParams = array(
        'dbname' => 'ums',
        'user' => 'sincro',
        'password' => 'Despecho2019**',
        'host' => '192.168.107.14',
        'port' => 3306,
        'charset' => 'utf8',
        'driver' => 'pdo_mysql',
      );

      $dbh = DriverManager::getConnection($connectionParams, $config);
      $qb = $dbh->createQueryBuilder();
      $qb->select('aa.id, aa.username, aa.personal_ID, aa.first_name, aa.last_name, aa.telephone, ad.name AS dep, aar.name')
        ->from('accounts_account', 'aa')
        ->innerJoin('aa', 'accounts_department', 'ad', 'ad.id = aa.department_id')
        ->innerJoin('ad', 'accounts_area', 'aar', 'aar.id = ad.area_id')
        ->where($qb->expr()->eq('aa.active', ':act'))
        ->andWhere($qb->expr()->eq('aa.username', ':us'))
        ->setParameter('us', $username)
        ->setParameter('act', 1);

      return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }


  public function findDatosCorreoUms($user)
  {
    $config = new Configuration();
    $connectionParams = array(
      'dbname' => 'ums',
      'user' => 'sincro',
      'password' => 'Despecho2019**',
      'host' => '192.168.107.14',
      'port' => 3306,
      'charset' => 'utf8',
      'driver' => 'pdo_mysql',
    );

    $dbh = DriverManager::getConnection($connectionParams, $config);
    $qb = $dbh->createQueryBuilder();
    $qb->select('aa.id, aa.mail_username')
      ->from('accounts_mailaccount', 'aa')
      ->innerJoin('aa', 'accounts_account', 'aar', 'aar.id = aa.id')
      ->where($qb->expr()->eq('aa.mail_active', ':act'))
      ->setParameter('act', 1);

    return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function findUserActive()
  {
    $delay = new \DateTime();
    $delay->setTimestamp(strtotime('2 minutes ago'));

    $qb = $this->createQueryBuilder('u')
      ->where('u.ultimaConexion > :delay')
      ->setParameter('delay', $delay)
    ;

    return $qb->getQuery()->getResult();
  }


  public function findAllActive()
  {
    $config = new Configuration();
//    $connectionParams = array(
//      'dbname' => 'ums',
//      'user' => 'admin',
//      'password' => 'MY@dmin.123',
//      'host' => 'mysql.camilo.sld.cu',
//      'port' => 3306,
//      'charset' => 'utf8',
//      'driver' => 'pdo_mysql',
//    );
   $connectionParams = array(
      'dbname' => 'ums',
     'user' => 'sincro',
     'password' => 'Despecho2019**',
      'host' => '192.168.107.14',
      'port' => 3306,
      'charset' => 'utf8',
      'driver' => 'pdo_mysql',
    );

    $dbh = DriverManager::getConnection($connectionParams, $config);
    $qb = $dbh->createQueryBuilder()
    ->select('aa.id, aa.username, aa.password, aa.personal_ID,aa.first_name,aa.last_name')
      ->from('accounts_account', 'aa')
      ->where('aa.active', ':act')
      ->setParameter('act', 1);

    return $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }
  public function findOwnedBy() {
    $d=$this->findAllActive();
    //$datos=new Arra
  foreach ($d as $l){

  }

  }
}
