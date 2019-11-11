<?php

namespace App\Controller;

use App\Service\CharacterFetcherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class ScreenController extends AbstractController {

  protected $characterFetcher;

  public function __construct(CharacterFetcherService $characterFetcher) {
    $this->characterFetcher = $characterFetcher;
  }

  /**
   * @Route(
   *   "/{characterId}",
   *   methods={"GET"},
   *   name="campaign_by_character"
   * )
   */
  public function campaignByCharacter(int $characterId) {
    $character = $this->characterFetcher->get($characterId);

    $characters = [$character];

    foreach ($character['campaign']['characters'] as $campaign_character) {
      if ($campaign_character['characterId'] != $characterId) {
        try {
          $characters[] = $this->characterFetcher->get($campaign_character['characterId']);
        }
        catch (ClientExceptionInterface $x) {
          continue;
        }
      }
    }

    return $this->render('sheet/sheet-cards.html.twig', [
      'characters' => $characters,
      'campaign' => $character['campaign'],
    ]);
  }

}
