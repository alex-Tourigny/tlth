/**
 * Shop button confetti burst on hover (inspired by https://codepen.io/aaroniker/pen/bGVGNrV)
 */
(function () {
	var COLORS = ['#ffc635', '#f1665d', '#48c0b6', '#5695d8'];
	var PARTICLE_COUNT = 40;
	var MIN_ANGLE = -125;
	var MAX_ANGLE = -55;
	var COOLDOWN_MS = 500;
	var GRAVITY = 520;
	var layer = null;

	var PARTICLE_STYLE =
		'position:absolute;display:block;width:6px;height:6px;margin:-3px 0 0 -3px;' +
		'border-radius:1px;pointer-events:none;transition:none;';

	function random(min, max) {
		return min + Math.random() * (max - min);
	}

	function getLayer() {
		if (layer && layer.isConnected) {
			return layer;
		}
		layer = document.getElementById('tlth-confetti-layer');
		if (!layer) {
			layer = document.createElement('div');
			layer.id = 'tlth-confetti-layer';
			layer.setAttribute('aria-hidden', 'true');
			layer.style.cssText =
				'position:fixed;inset:0;overflow:visible;pointer-events:none;z-index:99999;';
			document.body.appendChild(layer);
		}
		return layer;
	}

	function getOrigin(emitter) {
		var rect = emitter.getBoundingClientRect();
		return {
			x: rect.left + rect.width / 2,
			y: rect.top,
		};
	}

	function spawnParticle(mount, origin) {
		var angleDeg = random(MIN_ANGLE, MAX_ANGLE);
		var angle = (angleDeg * Math.PI) / 180;
		var speed = random(200, 360);
		var scale = random(0.55, 0.9);
		var dot = document.createElement('span');
		dot.className = 'shop-page-btn__particle';
		dot.style.cssText = PARTICLE_STYLE;
		dot.style.background = COLORS[Math.floor(Math.random() * COLORS.length)];
		dot.style.left = origin.x + 'px';
		dot.style.top = origin.y + 'px';
		dot.style.opacity = '1';
		mount.appendChild(dot);

		var x = 0;
		var y = 0;
		var vx = Math.cos(angle) * speed;
		var vy = Math.sin(angle) * speed;
		var rotation = random(0, 360);
		var rotVel = random(-540, 540);
		var life = 0;
		var duration = 1.4;
		var lastTime = null;

		function tick(now) {
			if (lastTime === null) {
				lastTime = now;
				requestAnimationFrame(tick);
				return;
			}
			var dt = Math.min((now - lastTime) / 1000, 0.05);
			lastTime = now;
			life += dt;

			var opacity = 1;
			if (life < 0.04) {
				opacity = life / 0.04;
			} else if (life > 0.7) {
				opacity = Math.max(0, 1 - (life - 0.7) / (duration - 0.7));
			}

			vy += GRAVITY * dt;
			x += vx * dt;
			y += vy * dt;
			rotation += rotVel * dt;

			dot.style.opacity = String(opacity);
			dot.style.left = origin.x + x + 'px';
			dot.style.top = origin.y + y + 'px';
			dot.style.transform = 'scale(' + scale + ') rotate(' + rotation + 'deg)';

			if (life < duration) {
				requestAnimationFrame(tick);
			} else {
				dot.remove();
			}
		}

		requestAnimationFrame(tick);
	}

	function spawnParticles(emitter) {
		var origin = getOrigin(emitter);
		var mount = getLayer();

		for (var i = 0; i < PARTICLE_COUNT; i++) {
			spawnParticle(mount, origin);
		}
	}

	function init() {
		document.querySelectorAll('.shop-page-btn').forEach(function (btn) {
			var emitter = btn.querySelector('.shop-page-btn__emitter');
			if (!emitter) {
				return;
			}

			var lastBurst = 0;

			btn.addEventListener('mouseenter', function () {
				var now = Date.now();
				if (now - lastBurst < COOLDOWN_MS) {
					return;
				}
				lastBurst = now;
				spawnParticles(emitter);
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
