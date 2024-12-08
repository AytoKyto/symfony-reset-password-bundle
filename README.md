# Symfony Reset Password Bundle

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)
![Symfony Version](https://img.shields.io/badge/symfony-%5E6.0-000000.svg)

Un bundle Symfony léger et autonome pour gérer la réinitialisation de mot de passe. Fournit une solution complète avec interface utilisateur intégrée, sans dépendances frontend supplémentaires.

## ✨ Caractéristiques

- 🔒 Système sécurisé de réinitialisation de mot de passe
- 📧 Envoi d'emails de réinitialisation personnalisables
- 🎨 Interface utilisateur moderne intégrée
- 🔄 Tokens de réinitialisation sécurisés avec expiration
- 📱 Design responsive
- 🚀 Installation simple sans configuration frontend

## 📋 Prérequis

- PHP 8.1 ou supérieur
- Symfony 6.0 ou supérieur
- Composer

## 🚀 Installation

1. Installer le bundle via Composer :

```bash
composer require ayto/reset-password-bundle
```

2. Ajouter le bundle dans `config/bundles.php` :

```php
return [
    // ...
    Ayto\ResetPasswordBundle\ResetPasswordBundle::class => ['all' => true],
];
```

3. Configurer le bundle dans `config/packages/reset_password.yaml` :

```yaml
reset_password:
    from_email: 'no-reply@example.com'  # Email utilisé pour envoyer les emails de réinitialisation
    token_lifetime: 3600                # Durée de validité du token en secondes (1 heure par défaut)
    user_class: App\Entity\User         # Votre classe User qui implémente ResetPasswordUserInterface
```

4. Implémenter l'interface dans votre entité User :

```php
use Ayto\ResetPasswordBundle\Model\ResetPasswordUserInterface;

class User implements ResetPasswordUserInterface
{
    private ?string $resetToken = null;
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    public function getResetPasswordToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetPasswordToken(?string $token): void
    {
        $this->resetToken = $token;
    }

    public function getResetPasswordTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetPasswordTokenExpiresAt(?\DateTimeInterface $expiresAt): void
    {
        $this->resetTokenExpiresAt = $expiresAt;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
```

5. Ajouter les routes dans `config/routes.yaml` :

```yaml
reset_password:
    resource: '@ResetPasswordBundle/config/routes.yaml'
```

## 🎨 Personnalisation

### Templates

Vous pouvez surcharger les templates en créant les fichiers suivants dans votre application :

- `templates/bundles/ResetPasswordBundle/reset_password/request.html.twig`
- `templates/bundles/ResetPasswordBundle/reset_password/reset.html.twig`
- `templates/bundles/ResetPasswordBundle/emails/reset_password.html.twig`

### Styles

Les styles CSS sont inclus directement dans les templates et peuvent être surchargés en modifiant les templates ou en ajoutant vos propres styles.

## 🔒 Sécurité

- Génération sécurisée des tokens
- Validation des mots de passe côté client et serveur
- Protection contre les attaques par force brute
- Expiration automatique des tokens
- Invalidation des anciens tokens

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forker le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commiter vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pusher sur la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 License

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.
