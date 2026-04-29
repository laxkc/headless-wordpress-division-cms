/**
 * On-demand revalidation hook called by WordPress (northium-cms/includes/webhook.php).
 *
 * Expected payload: { type, slug, action }
 *   type    one of: division | service | advisor | article | campaign
 *   slug    post_name of the affected post
 *   action  "save" | "delete"
 *
 * Auth: Authorization: Bearer <REVALIDATE_SECRET>
 */

import { NextRequest, NextResponse } from "next/server";
import { revalidateTag } from "next/cache";
import { env } from "@/lib/env";

const TAGS_BY_TYPE: Record<string, (slug: string) => string[]> = {
  division: (slug) => ["divisions", `division:${slug}`],
  service: (slug) => ["services", `service:${slug}`, "divisions"],
  advisor: (slug) => ["advisors", `advisor:${slug}`, "divisions"],
  article: (slug) => ["articles", `article:${slug}`, "divisions"],
  campaign: (slug) => ["campaigns", `campaign:${slug}`, "divisions"],
};

export async function POST(request: NextRequest) {
  const auth = request.headers.get("authorization");
  if (auth !== `Bearer ${env.REVALIDATE_SECRET}`) {
    return NextResponse.json({ error: "unauthorized" }, { status: 401 });
  }

  let body: unknown;
  try {
    body = await request.json();
  } catch {
    return NextResponse.json({ error: "invalid json" }, { status: 400 });
  }

  const { type, slug, action } = body as {
    type?: string;
    slug?: string;
    action?: string;
  };

  if (!type || !slug) {
    return NextResponse.json(
      { error: "missing type or slug" },
      { status: 400 }
    );
  }

  const buildTags = TAGS_BY_TYPE[type];
  if (!buildTags) {
    return NextResponse.json(
      { error: `unknown type: ${type}` },
      { status: 400 }
    );
  }

  const tags = buildTags(slug);
  for (const tag of tags) {
    revalidateTag(tag, "max");
  }

  return NextResponse.json({
    ok: true,
    type,
    slug,
    action,
    revalidated: tags,
  });
}
