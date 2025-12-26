# Docker 설치 및 실행 가이드

## 📋 필수 사항
- Docker 설치 (https://www.docker.com/get-started)
- Docker Compose 설치 (Docker Desktop에 포함)

## 🚀 빠른 시작

### 1. 환경 설정 파일 생성
```bash
cp .env.example .env
```

`.env` 파일을 열어서 필요한 값들을 수정하세요.

### 2. Docker 컨테이너 빌드 및 실행
```bash
# 컨테이너 빌드 및 시작
docker-compose up -d --build

# 로그 확인
docker-compose logs -f
```

### 3. 접속 확인
- **웹 애플리케이션**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - 사용자명: `finwooyi_user`
  - 비밀번호: `finwooyi_pass`

## 📝 주요 명령어

### 컨테이너 관리
```bash
# 컨테이너 시작
docker-compose up -d

# 컨테이너 중지
docker-compose down

# 컨테이너 재시작
docker-compose restart

# 컨테이너 상태 확인
docker-compose ps

# 로그 실시간 보기
docker-compose logs -f web
```

### 컨테이너 내부 접속
```bash
# 웹 서버 컨테이너 접속
docker-compose exec web bash

# 데이터베이스 컨테이너 접속
docker-compose exec db mysql -u finwooyi_user -p
```

### 데이터베이스 관리
```bash
# 데이터베이스 백업
docker-compose exec db mysqldump -u finwooyi_user -pfinwooyi_pass finwooyi_db > backup.sql

# 데이터베이스 복원
docker-compose exec -T db mysql -u finwooyi_user -pfinwooyi_pass finwooyi_db < backup.sql
```

## 🔧 개발 환경 설정

### Composer 패키지 설치
```bash
docker-compose exec web composer install
```

### npm 패키지 설치 및 빌드
```bash
docker-compose exec web npm install
docker-compose exec web npm run build
```

### Gulp watch (개발 중 자동 빌드)
```bash
docker-compose exec web npx gulp watch
```

## 📂 프로젝트 구조
```
finwooyi/
├── Dockerfile              # 웹 서버 Docker 이미지 설정
├── docker-compose.yml      # Docker Compose 설정
├── .dockerignore          # Docker 빌드 시 제외할 파일
├── .env.example           # 환경 변수 예제
├── .env                   # 환경 변수 (직접 생성)
└── application/
    ├── cache/             # 캐시 파일 (쓰기 권한 필요)
    └── logs/              # 로그 파일 (쓰기 권한 필요)
```

## ⚙️ 설정 수정

### 데이터베이스 연결 설정
`application/config/database.php` 파일에서:
```php
$db['default'] = array(
    'hostname' => 'db',  // docker-compose의 서비스명
    'username' => 'finwooyi_user',
    'password' => 'finwooyi_pass',
    'database' => 'finwooyi_db',
    'dbdriver' => 'mysqli',
    // ... 기타 설정
);
```

### 포트 변경
`docker-compose.yml` 파일에서 포트를 변경할 수 있습니다:
```yaml
services:
  web:
    ports:
      - "8080:80"  # 왼쪽 숫자를 변경 (예: "9000:80")
```

## 🐛 문제 해결

### 권한 오류
```bash
# 캐시 및 로그 폴더 권한 설정
docker-compose exec web chmod -R 777 application/cache application/logs
```

### 포트 충돌
다른 프로그램이 이미 포트를 사용 중인 경우:
```bash
# 사용 중인 포트 확인 (Mac/Linux)
lsof -i :8080

# 사용 중인 포트 확인 (Windows)
netstat -ano | findstr :8080
```

### 컨테이너 완전 재시작
```bash
# 모든 컨테이너 및 볼륨 삭제 후 재시작
docker-compose down -v
docker-compose up -d --build
```

## 🔒 보안 주의사항

1. **운영 환경에서는 반드시 변경하세요:**
   - 데이터베이스 비밀번호
   - CodeIgniter encryption_key
   - phpMyAdmin 포트 (또는 비활성화)

2. **.env 파일을 Git에 커밋하지 마세요**

3. **운영 환경에서는 DEBUG 모드를 끄세요**

## 📚 추가 정보
- [Docker 공식 문서](https://docs.docker.com/)
- [Docker Compose 가이드](https://docs.docker.com/compose/)
- [CodeIgniter 3 문서](https://codeigniter.com/userguide3/)

## 💡 팁
- 개발 중에는 볼륨 마운트로 실시간 코드 반영
- 운영 배포 시에는 코드를 이미지에 복사하여 사용
- 정기적으로 데이터베이스 백업 수행