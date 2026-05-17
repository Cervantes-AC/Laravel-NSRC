import { DutySession } from '../types';

/**
 * Normalize a name for comparison
 * - Convert to lowercase
 * - Remove extra whitespace
 * - Remove common titles/prefixes
 */
export const normalizeName = (name: string): string => {
  if (!name || typeof name !== 'string') return '';
  
  return name
    .toLowerCase()
    .trim()
    .replace(/\s+/g, ' ')
    .replace(/^(mr|mrs|ms|dr|prof|sir|madam)\s+/i, '')
    .replace(/\s+(jr|sr|ii|iii|iv|v)$/i, '');
};

/**
 * Calculate similarity score between two names (0-100)
 * Uses a combination of:
 * - Levenshtein distance
 * - Token overlap
 * - Substring matching
 */
export const getNameSimilarityScore = (name1: string, name2: string): number => {
  const n1 = normalizeName(name1);
  const n2 = normalizeName(name2);

  if (n1 === n2) return 100;
  if (!n1 || !n2) return 0;

  // Exact substring match
  if (n1.includes(n2) || n2.includes(n1)) return 95;

  // Token-based matching (for reordered names like "John Doe" vs "Doe John")
  const tokens1 = n1.split(' ').filter(t => t.length > 0);
  const tokens2 = n2.split(' ').filter(t => t.length > 0);

  const commonTokens = tokens1.filter(t => tokens2.includes(t)).length;
  const totalTokens = Math.max(tokens1.length, tokens2.length);
  const tokenScore = (commonTokens / totalTokens) * 100;

  // Levenshtein distance
  const levenScore = 100 - (levenshteinDistance(n1, n2) / Math.max(n1.length, n2.length)) * 100;

  // Weighted average: 60% token, 40% levenshtein
  return Math.round(tokenScore * 0.6 + levenScore * 0.4);
};

/**
 * Calculate Levenshtein distance between two strings
 */
const levenshteinDistance = (s1: string, s2: string): number => {
  const len1 = s1.length;
  const len2 = s2.length;
  const matrix: number[][] = [];

  for (let i = 0; i <= len2; i++) {
    matrix[i] = [i];
  }

  for (let j = 0; j <= len1; j++) {
    matrix[0][j] = j;
  }

  for (let i = 1; i <= len2; i++) {
    for (let j = 1; j <= len1; j++) {
      if (s2.charAt(i - 1) === s1.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1];
      } else {
        matrix[i][j] = Math.min(
          matrix[i - 1][j - 1] + 1,
          matrix[i][j - 1] + 1,
          matrix[i - 1][j] + 1
        );
      }
    }
  }

  return matrix[len2][len1];
};

/**
 * Check if two names are similar enough to be considered the same person
 */
export const areNamesSimilar = (name1: string, name2: string, threshold: number = 0.85): boolean => {
  const score = getNameSimilarityScore(name1, name2);
  return score >= threshold * 100;
};

/**
 * Merge sessions with similar names
 * Groups sessions by canonical name and returns merged list
 */
export const mergeSimilarNameSessions = (sessions: DutySession[]): DutySession[] => {
  if (sessions.length === 0) return [];

  const nameMap: Record<string, string> = {}; // Maps normalized names to canonical names
  const result: DutySession[] = [];

  sessions.forEach(session => {
    const normalized = normalizeName(session.fullName);

    // Check if we've already seen a similar name
    let canonicalName = nameMap[normalized];

    if (!canonicalName) {
      // Look for similar names in existing map
      let bestMatch: { name: string; score: number } | null = null;

      for (const [existingNorm, canonical] of Object.entries(nameMap)) {
        const score = getNameSimilarityScore(session.fullName, canonical);
        if (score >= 85 && (!bestMatch || score > bestMatch.score)) {
          bestMatch = { name: canonical, score };
        }
      }

      if (bestMatch) {
        canonicalName = bestMatch.name;
      } else {
        canonicalName = session.fullName;
      }

      nameMap[normalized] = canonicalName;
    }

    // Add session with canonical name
    result.push({
      ...session,
      fullName: canonicalName,
    });
  });

  return result;
};

/**
 * Get unique volunteer names from sessions
 */
export const getUniqueVolunteerNames = (sessions: DutySession[]): string[] => {
  const merged = mergeSimilarNameSessions(sessions);
  return [...new Set(merged.map(s => s.fullName))].sort();
};
